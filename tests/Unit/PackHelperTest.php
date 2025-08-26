<?php

namespace Tests\Unit;

use App\Helpers\PackHelper;
use App\Models\Pack;
use App\Models\Matiere;
use App\Models\Vente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackHelperTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_calculate_pack_statistics()
    {
        // Créer des packs de test
        Pack::factory()->count(3)->create([
            'prix' => 100,
            'est_actif' => true,
        ]);
        
        // Un pack inactif
        Pack::factory()->create([
            'prix' => 200,
            'est_actif' => false,
        ]);
        
        $stats = PackHelper::calculateStatistics();
        
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(3, $stats['actifs']);
        $this->assertEquals(1, $stats['inactifs']);
        $this->assertEquals(125, $stats['prix_moyen']); // (100*3 + 200) / 4 = 125
    }

    /** @test */
    public function it_can_format_price()
    {
        $this->assertEquals('100,00 DH', PackHelper::formatPrice(100));
        $this->assertEquals('1 000,50 DH', PackHelper::formatPrice(1000.5));
        $this->assertEquals('0,00 DH', PackHelper::formatPrice(0));
    }

    /** @test */
    public function it_can_generate_slug()
    {
        $title = 'Pack de test avec des espaces';
        $slug = PackHelper::generateSlug($title);
        
        $this->assertEquals('pack-de-test-avec-des-espaces', $slug);
        
        // Vérifier que le slug est unique
        Pack::factory()->create(['slug' => $slug]);
        $newSlug = PackHelper::generateSlug($title);
        
        $this->assertStringStartsWith('pack-de-test-avec-des-espaces-', $newSlug);
        $this->assertNotEquals($slug, $newSlug);
    }

    /** @test */
    public function it_can_get_pack_types()
    {
        $types = PackHelper::getPackTypes();
        
        $this->assertIsArray($types);
        $this->assertArrayHasKey('cours', $types);
        $this->assertArrayHasKey('abonnement', $types);
        $this->assertArrayHasKey('formation', $types);
        $this->assertArrayHasKey('autre', $types);
    }

    /** @test */
    public function it_can_check_if_pack_can_be_deleted()
    {
        $pack = Pack::factory()->create();
        
        // Le pack devrait être supprimable s'il n'a pas de ventes
        $this->assertTrue(PackHelper::canDeletePack($pack->id));
        
        // Créer une vente pour ce pack
        Vente::factory()->create([
            'pack_id' => $pack->id,
            'montant' => $pack->prix,
        ]);
        
        // Le pack ne devrait plus être supprimable
        $this->assertFalse(PackHelper::canDeletePack($pack->id));
    }

    /** @test */
    public function it_can_get_similar_packs()
    {
        // Créer des packs de différents types
        $pack1 = Pack::factory()->create([
            'type' => 'cours',
            'prix' => 100,
            'est_actif' => true,
        ]);
        
        $pack2 = Pack::factory()->create([
            'type' => 'cours',
            'prix' => 150,
            'est_actif' => true,
        ]);
        
        // Un pack d'un type différent
        Pack::factory()->create([
            'type' => 'abonnement',
            'prix' => 200,
            'est_actif' => true,
        ]);
        
        // Un pack inactif
        Pack::factory()->create([
            'type' => 'cours',
            'prix' => 120,
            'est_actif' => false,
        ]);
        
        $similarPacks = PackHelper::getSimilarPacks($pack1->id, 2);
        
        // Devrait retourner 1 pack similaire (l'autre pack du même type)
        $this->assertCount(1, $similarPacks);
        $this->assertEquals($pack2->id, $similarPacks->first()->id);
    }

    /** @test */
    public function it_can_get_popular_packs()
    {
        // Créer des packs populaires et non populaires
        $popularPacks = Pack::factory()->count(3)->create([
            'est_populaire' => true,
            'est_actif' => true,
        ]);
        
        Pack::factory()->count(2)->create([
            'est_populaire' => false,
            'est_actif' => true,
        ]);
        
        $result = PackHelper::getPopularPacks(2);
        
        // Devrait retourner 2 packs populaires
        $this->assertCount(2, $result);
        $this->assertTrue($result->every(function ($pack) {
            return $pack->est_populaire === true;
        }));
    }

    /** @test */
    public function it_can_get_display_price()
    {
        // Test avec un prix promotionnel
        $packWithPromo = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => 80,
        ]);
        
        $this->assertEquals(80, PackHelper::getDisplayPrice($packWithPromo));
        
        // Test sans prix promotionnel
        $packWithoutPromo = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => null,
        ]);
        
        $this->assertEquals(100, PackHelper::getDisplayPrice($packWithoutPromo));
    }

    /** @test */
    public function it_can_calculate_discount_percentage()
    {
        // Test avec une réduction
        $packWithDiscount = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => 80,
        ]);
        
        $this->assertEquals(20, PackHelper::calculateDiscountPercentage($packWithDiscount));
        
        // Test sans réduction
        $packWithoutDiscount = Pack::factory()->create([
            'prix' => 100,
            'prix_promo' => null,
        ]);
        
        $this->assertNull(PackHelper::calculateDiscountPercentage($packWithoutDiscount));
    }
}
