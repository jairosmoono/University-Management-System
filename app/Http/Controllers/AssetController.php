<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\Department;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::active()->get();
        $assets = Asset::with('department')
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->status,        fn($q) => $q->where('status', $request->status))
            ->when($request->category,      fn($q) => $q->where('category', $request->category))
            ->when($request->search,        fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('asset_code', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total'              => Asset::count(),
            'active'             => Asset::where('status', 'active')->count(),
            'maintenance'        => Asset::where('status', 'maintenance')->count(),
            'disposed'           => Asset::where('status', 'disposed')->count(),
            'total_purchase'     => Asset::sum('purchase_price'),
            'total_book_value'   => Asset::sum('current_value'),
        ];

        return view('assets.index', compact('assets', 'departments', 'stats'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        return view('assets.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'asset_code'           => 'required|string|unique:assets',
            'category'             => 'required|string|max:100',
            'description'          => 'nullable|string',
            'department_id'        => 'nullable|exists:departments,id',
            'purchase_date'        => 'nullable|date',
            'purchase_price'       => 'nullable|numeric|min:0',
            'current_value'        => 'nullable|numeric|min:0',
            'status'               => 'required|in:active,maintenance,disposed,lost',
            'location'             => 'nullable|string|max:255',
            'serial_number'        => 'nullable|string|max:100',
            'warranty_expiry'      => 'nullable|date',
            'depreciation_method'  => 'nullable|in:straight_line,declining_balance',
            'depreciation_rate'    => 'nullable|numeric|min:0|max:100',
            'useful_life_years'    => 'nullable|integer|min:1|max:100',
            'salvage_value'        => 'nullable|numeric|min:0',
        ]);

        // Default current_value to purchase_price if not set
        if (empty($validated['current_value']) && !empty($validated['purchase_price'])) {
            $validated['current_value'] = $validated['purchase_price'];
        }

        $asset = Asset::create($validated);
        return redirect()->route('assets.show', $asset)->with('success', "Asset '{$asset->name}' created successfully.");
    }

    public function show(Asset $asset)
    {
        $asset->load(['department', 'depreciationLogs.recordedBy']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $departments = Department::active()->get();
        return view('assets.create', compact('asset', 'departments'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'asset_code'           => "required|string|unique:assets,asset_code,{$asset->id}",
            'category'             => 'required|string|max:100',
            'description'          => 'nullable|string',
            'department_id'        => 'nullable|exists:departments,id',
            'purchase_date'        => 'nullable|date',
            'purchase_price'       => 'nullable|numeric|min:0',
            'current_value'        => 'nullable|numeric|min:0',
            'status'               => 'required|in:active,maintenance,disposed,lost',
            'location'             => 'nullable|string|max:255',
            'serial_number'        => 'nullable|string|max:100',
            'warranty_expiry'      => 'nullable|date',
            'depreciation_method'  => 'nullable|in:straight_line,declining_balance',
            'depreciation_rate'    => 'nullable|numeric|min:0|max:100',
            'useful_life_years'    => 'nullable|integer|min:1|max:100',
            'salvage_value'        => 'nullable|numeric|min:0',
        ]);

        $asset->update($validated);
        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted.');
    }

    public function assign(Request $request, Asset $asset)
    {
        $request->validate(['department_id' => 'required|exists:departments,id']);
        $asset->update(['department_id' => $request->department_id]);
        return back()->with('success', 'Asset assigned to department.');
    }

    // Record a depreciation entry for a single asset
    public function recordDepreciation(Request $request, Asset $asset)
    {
        $request->validate([
            'period_label'        => 'required|string|max:20',
            'depreciation_amount' => 'required|numeric|min:0.01',
            'notes'               => 'nullable|string|max:500',
        ]);

        $opening = (float) ($asset->current_value ?? $asset->purchase_price ?? 0);
        $amount  = (float) $request->depreciation_amount;
        $salvage = (float) ($asset->salvage_value ?? 0);

        // Never depreciate below salvage value
        $amount  = min($amount, max(0, $opening - $salvage));

        if ($amount <= 0) {
            return back()->with('error', 'Asset is already at or below its salvage value — no depreciation recorded.');
        }

        $closing = $opening - $amount;

        AssetDepreciation::create([
            'asset_id'            => $asset->id,
            'period_label'        => $request->period_label,
            'method'              => $asset->depreciation_method ?? 'manual',
            'opening_value'       => $opening,
            'depreciation_amount' => $amount,
            'closing_value'       => $closing,
            'notes'               => $request->notes,
            'recorded_by'         => auth()->id(),
        ]);

        // Update asset's current book value
        $asset->update(['current_value' => $closing]);

        return back()->with('success', "Depreciation of " . formatCurrency($amount) . " recorded for period {$request->period_label}.");
    }

    // Platform-wide depreciation report
    public function depreciationReport(Request $request)
    {
        $query = Asset::with('department')
            ->whereNotNull('depreciation_method')
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->category,      fn($q) => $q->where('category', $request->category))
            ->when($request->method,        fn($q) => $q->where('depreciation_method', $request->method));

        $assets = $query->orderBy('name')->get();

        $departments = Department::active()->orderBy('name')->get();

        $totals = [
            'purchase'     => $assets->sum('purchase_price'),
            'book_value'   => $assets->sum('current_value'),
            'accumulated'  => $assets->sum(fn($a) => $a->accumulatedDepreciation()),
            'annual_dep'   => $assets->sum(fn($a) => $a->annualDepreciation()),
        ];

        return view('assets.depreciation-report', compact('assets', 'departments', 'totals'));
    }
}
