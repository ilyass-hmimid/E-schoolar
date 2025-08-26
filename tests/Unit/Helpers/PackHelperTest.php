<?php

namespace Tests\Unit\Helpers;

use App\Helpers\PackHelper;
use App\Models\Pack;
use App\Models\Vente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackHelperTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_discount_percentage_correctly()
    {
        // Test avec une remise de 20%
        $this->assertEquals(20, PackHelper::calculateDiscountPercentage(100, 80));
        
        // Test avec une remise de 50%
        $this->assertEquals(50, PackHelper::calculateDiscountPercentage(200, 100));
        
        // Test sans remise
        $this->assertEquals(0, PackHelper::calculateDiscountPercentage(100, 100));
        
        // Test avec prix promotionnel supérieur au prix normal
        $this->assertEquals(0, PackHelper::calculateDiscountPercentage(100, 120));
    }

    /** @test */
    public function it_formats_price_correctly()
    {
        $this->assertEquals('1 234,56 DH', PackHelper::formatPrice(1234.56));
        $this->assertEquals('1 000,00 DH', PackHelper::formatPrice(1000));
        $this->assertEquals('0,50 DH', PackHelper::formatPrice(0.5));
    }

    /** @test */
    public function it_generates_unique_slugs()
    {
        $name = 'Pack de test';
        
        // Premier appel - devrait retourner le slug de base
        $slug1 = PackHelper::generateSlug($name);
        $this->assertEquals('pack-de-test', $slug1);
        
        // Créer un pack avec le même slug
        Pack::factory()->create(['slug' => $slug1]);
        
        // Deuxième appel - devrait ajouter un suffixe numérique
        $slug2 = PackHelper::generateSlug($name);
        $this->assertEquals('pack-de-test-1', $slug2);
        
        // Créer un autre pack avec le deuxième slug
        Pack::factory()->create(['slug' => $slug2]);
        
        // Troisième appel - devrait incrémenter le suffixe
        $slug3 = PackHelper::generateSlug($name);
        $this->assertEquals('pack-de-test-2', $slug3);
    }

    /** @test */
    public function it_gets_display_price_correctly()
    {
        $pack = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => 80
        ]);
        
        $this->assertEquals(80, PackHelper::getDisplayPrice($pack));
        
        // Sans prix promotionnel, devrait retourner le prix normal
        $pack->prix_promo = null;
        $this->assertEquals(100, PackHelper::getDisplayPrice($pack));
    }

    /** @test */
    public function it_checks_if_pack_can_be_deleted()
    {
        $pack = Pack::factory()->create();
        
        // Nouveau pack sans ventes ni inscriptions - devrait pouvoir être supprimé
        $this->assertTrue(PackHelper::canBeDeleted($pack));
        
        // Simuler une vente associée
        $pack->ventes()->attach(1, ['prix' => 100, 'quantite' => 1]);
        
        // Recharger le modèle pour recharger les relations
        $pack->refresh();
        
        // Ne devrait pas pouvoir être supprimé car il a des ventes associées
        $this->assertFalse(PackHelper::canBeDeleted($pack));
    }

    /** @test */
    public function it_gets_similar_packs()
    {
        // Créer un pack de type 'cours'
        $pack = Pack::factory()->type('cours')->create();
        
        // Créer d'autres packs du même type
        $similarPacks = Pack::factory()->count(3)->type('cours')->create();
        
        // Créer des packs d'un type différent (ne devraient pas être inclus)
        Pack::factory()->count(2)->type('abonnement')->create();
        
        $result = PackHelper::getSimilarPacks($pack, 2);
        
        $this->assertCount(2, $result);
        $this->assertNotContains($pack->id, $result->pluck('id'));
        $result->each(function ($similarPack) {
            $this->assertEquals('cours', $similarPack->type);
        });
    }

    /** @test */
    public function it_gets_pack_stats()
    {
        // Créer des packs avec différents statuts
        Pack::factory()->count(3)->active()->create(['prix' => 100]);
        Pack::factory()->count(2)->inactive()->create(['prix' => 200]);
        
        // Créer des ventes pour les statistiques
        $pack = Pack::first();
        $vente = Vente::factory()->create();
        $pack->ventes()->attach($vente->id, ['prix' => 100, 'quantite' => 1]);
        
        $stats = PackHelper::getStats();
        
        $this->assertEquals(5, $stats['total']);
        $this->assertEquals(3, $stats['active']);
        $this->assertEquals(140, $stats['average_price']); // (300 + 400) / 5 = 140
        $this->assertEquals(100, $stats['total_sales']);
    }

    /** @test */
    public function it_gets_popular_packs()
    {
        // Créer des packs avec différents niveaux de popularité
        $popularPacks = Pack::factory()->count(5)->popular()->create();
        $regularPacks = Pack::factory()->count(5)->create();
        
        // Ajouter des ventes pour les packs populaires
        $vente = Vente::factory()->create();
        foreach ($popularPacks as $pack) {
            $pack->ventes()->attach($vente->id, ['prix' => 100, 'quantite' => 1]);
        }
        
        $result = PackHelper::getPopularPacks(3);
        
        $this->assertCount(3, $result);
        $result->each(function ($pack) use ($popularPacks) {
            $this->assertTrue($popularPacks->contains('id', $pack->id));
        });
    }

    /** @test */
    public function it_gets_pack_types()
    {
        $types = PackHelper::getPackTypes();
        
        $this->assertIsArray($types);
        $this->assertArrayHasKey('cours', $types);
        $this->assertArrayHasKey('abonnement', $types);
        $this->assertArrayHasKey('formation', $types);
        $this->assertArrayHasKey('autre', $types);
        
        // Vérifier la structure de chaque type
        foreach ($types as $type) {
            $this->assertArrayHasKey('name', $type);
            $this->assertArrayHasKey('icon', $type);
            $this->assertArrayHasKey('color', $type);
        }
    }
}
