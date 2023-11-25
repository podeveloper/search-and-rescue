<?php

namespace App\Observers;

use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\StockMovement;
use App\Models\StockPlace;

class StockMovementObserver
{
    /**
     * Handle the StockMovement "created" event.
     */
    public function created(StockMovement $stockMovement): void
    {
        $this->updateMaterialStock($stockMovement->fromWhere, $stockMovement->material, -$stockMovement->amount);
        $this->updateMaterialStock($stockMovement->toWhere, $stockMovement->material, $stockMovement->amount);
    }

    /**
     * Handle the StockMovement "updated" event.
     */
    public function updated(StockMovement $stockMovement): void
    {
            $oldMaterial = Material::find($stockMovement->getOriginal('material_id'));
            $oldFrom = StockPlace::find($stockMovement->getOriginal('from_where'));
            $oldTo = StockPlace::find($stockMovement->getOriginal('to_where'));
            $oldAmount = $stockMovement->getOriginal('amount');

            $newMaterial = Material::find($stockMovement->material_id);
            $newFrom = StockPlace::find($stockMovement->from_where);
            $newTo = StockPlace::find($stockMovement->to_where);
            $newAmount = $stockMovement->amount;

            // Rollback the previous operation
            $this->updateMaterialStock($oldFrom, $oldMaterial, $oldAmount); // Increase The "From" Place
            $this->updateMaterialStock($oldTo, $oldMaterial, -$oldAmount); // Decrease The "To" Place

            // Re-transfer the updated amount
            $this->updateMaterialStock($newFrom, $newMaterial, -$newAmount);
            $this->updateMaterialStock($newTo, $newMaterial, $newAmount);
    }

    /**
     * Handle the StockMovement "deleted" event.
     */
    public function deleted(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "restored" event.
     */
    public function restored(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "force deleted" event.
     */
    public function forceDeleted(StockMovement $stockMovement): void
    {
        //
    }

    private function updateMaterialStock(StockPlace $stockPlace, Material $material, int $amountChange): void
    {
        $materialStock = MaterialStock::query()
            ->where('stock_place_id', $stockPlace->id)
            ->where('material_id', $material->id)
            ->first();

        if (!$materialStock)
        {
            $materialStock = MaterialStock::create([
                'stock_place_id' => $stockPlace->id,
                'material_id' => $material->id,
                'lower_limit' => 0,
                'current_amount' => 0,
            ]);
        }

        $current_amount = $materialStock->current_amount + $amountChange;
        if ($current_amount < 0) $current_amount = 0;

        $materialStock->update(['current_amount' => $current_amount]);
    }
}
