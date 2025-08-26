<?php

namespace Tests\Unit;

use App\Models\Pack;
use App\Models\Matiere;
use App\Models\Vente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_pack()
    {
        $pack = Pack::factory()->create([
            'nom' => 'Pack Test',
            'type' => 'cours',
            'prix' => 99.99,
            'est_actif' => true,
        ]);

        $this->assertDatabaseHas('packs', [
            'nom' => 'Pack Test',
            'type' => 'cours',
            'prix' => 99.99,
            'est_actif' => true,
        ]);
    }

    /** @test */
    public function it_can_have_many_matieres()
    {
        $pack = Pack::factory()->create();
        $matieres = Matiere::factory()->count(3)->create();
        
        $pack->matieres()->attach($matieres->pluck('id'));
        
        $this->assertCount(3, $pack->matieres);
    }

    /** @test */
    public function it_can_have_many_ventes()
    {
        $pack = Pack::factory()->create();
        $ventes = Vente::factory()->count(2)->create();
        
        // Attacher les ventes au pack via la table de liaison
        foreach ($ventes as $vente) {
            $pack->ventes()->attach($vente->id, [
                'prix_unitaire' => $pack->prix,
                'quantite' => 1,
                'prix_total' => $pack->prix
            ]);
        }
        
        $this->assertCount(2, $pack->fresh()->ventes);
    }

    /** @test */
    public function it_can_check_if_pack_is_deletable()
    {
        $pack = Pack::factory()->create();
        
        // Le pack devrait être supprimable s'il n'a pas de ventes
        $this->assertTrue($pack->isDeletable());
        
        // Créer et attacher une vente au pack
        $vente = Vente::factory()->create();
        $pack->ventes()->attach($vente->id, [
            'prix_unitaire' => $pack->prix,
            'quantite' => 1,
            'prix_total' => $pack->prix
        ]);
        
        // Recharger le pack depuis la base de données
        $pack->refresh();
        
        // Le pack ne devrait plus être supprimable car il a une vente
        $this->assertFalse($pack->isDeletable());
    }

    /** @test */
    public function it_can_get_display_price()
    {
        // Test avec un prix promotionnel
        $packWithPromo = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => 80,
        ]);
        $this->assertEquals(80, $packWithPromo->getDisplayPrice());
        
        // Test sans prix promotionnel
        $packWithoutPromo = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => null,
        ]);
        $this->assertEquals(100, $packWithoutPromo->getDisplayPrice());
    }

    /** @test */
    public function it_can_get_discount_percentage()
    {
        // Test avec une réduction
        $packWithDiscount = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => 80,
        ]);
        $this->assertEquals(20, $packWithDiscount->getDiscountPercentage());
        
        // Test sans réduction
        $packWithoutDiscount = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => null,
        ]);
        $this->assertNull($packWithoutDiscount->getDiscountPercentage());
    }

    /** @test */
    public function it_can_scope_active()
    {
        $activePack = Pack::factory()->create(['est_actif' => true]);
        $inactivePack = Pack::factory()->create(['est_actif' => false]);
        
        $activePacks = Pack::active()->get();
        
        $this->assertTrue($activePacks->contains($activePack));
        $this->assertFalse($activePacks->contains($inactivePack));
    }

    /** @test */
    public function it_can_scope_popular()
    {
        $popularPack = Pack::factory()->create(['est_populaire' => true]);
        $regularPack = Pack::factory()->create(['est_populaire' => false]);
        
        $popularPacks = Pack::popular()->get();
        
        $this->assertTrue($popularPacks->contains($popularPack));
        $this->assertFalse($popularPacks->contains($regularPack));
    }
}
