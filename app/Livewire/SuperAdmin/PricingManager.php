<?php

namespace App\Livewire\SuperAdmin;

use App\Models\PricingPlan;
use App\Services\PricingCatalogService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.superadmin')]
class PricingManager extends Component
{
    public ?int $editingId = null;
    public string $planKey = '';
    public string $name = '';
    public string $subtitle = '';
    public int $price = 0;
    public string $description = '';
    public string $cta = '';
    public ?int $order_limit = null;
    public ?int $max_outlets = null;
    public ?int $quota = null;
    public bool $is_published = true;
    public int $sort_order = 10;
    public string $featuresText = '';

    public function mount(PricingCatalogService $pricingCatalogService): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
        $pricingCatalogService->syncDefaults();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'planKey' => ['required', 'string', 'max:50', $this->editingId ? 'unique:pricing_plans,key,' . $this->editingId : 'unique:pricing_plans,key'],
            'name' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'cta' => ['nullable', 'string', 'max:255'],
            'order_limit' => ['nullable', 'integer', 'min:0'],
            'max_outlets' => ['nullable', 'integer', 'min:0'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['key'] = strtolower(trim($this->planKey));
        unset($validated['planKey']);
        $validated['features'] = collect(preg_split('/\r\n|\r|\n/', $this->featuresText))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();

        PricingPlan::updateOrCreate(
            ['id' => $this->editingId],
            $validated,
        );

        session()->flash('success', $this->editingId ? 'Harga berhasil diperbarui.' : 'Harga berhasil ditambahkan.');
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $plan = PricingPlan::findOrFail($id);

        $this->editingId = $plan->id;
        $this->planKey = $plan->key;
        $this->name = $plan->name;
        $this->subtitle = $plan->subtitle ?? '';
        $this->price = (int) $plan->price;
        $this->description = $plan->description ?? '';
        $this->cta = $plan->cta ?? '';
        $this->order_limit = $plan->order_limit;
        $this->max_outlets = $plan->max_outlets;
        $this->quota = $plan->quota;
        $this->is_published = (bool) $plan->is_published;
        $this->sort_order = (int) $plan->sort_order;
        $this->featuresText = implode(PHP_EOL, $plan->features ?? []);
    }

    public function delete(int $id): void
    {
        PricingPlan::findOrFail($id)->delete();
        session()->flash('success', 'Harga berhasil dihapus.');

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId',
            'planKey',
            'name',
            'subtitle',
            'price',
            'description',
            'cta',
            'order_limit',
            'max_outlets',
            'quota',
            'featuresText',
        ]);

        $this->is_published = true;
        $this->sort_order = 10;
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $plans = PricingPlan::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('livewire.super-admin.pricing-manager', [
            'plans' => $plans,
        ]);
    }
}
