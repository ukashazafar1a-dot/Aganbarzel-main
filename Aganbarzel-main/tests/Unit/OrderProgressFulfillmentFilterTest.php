<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\OrderProgressService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class OrderProgressFulfillmentFilterTest extends TestCase
{
    /**
     * @return list<array<string, mixed>>
     */
    private function templatesForOrderTags(string $tags, bool $filterEnabled = true): array
    {
        Config::set('order-progress.use_product_metafield_checklist', false);
        Config::set('order-progress.filter_fulfillment_options_by_flow_tag', $filterEnabled);
        Config::set('order-progress.fulfillment_choice_tags', [
            'pickup' => 'אריה שנקר 26',
            'delivery' => 'משלוח עד הבית',
            'delivery_installation' => 'משלוח + התקנה',
        ]);
        Config::set('order-progress.steps', [
            [
                'key' => 'suppliers_ordered',
                'label_he' => 'הוזמנו חלקים אצל הספקים',
                'tag' => 'stage-suppliers-ordered',
                'position' => 0,
                'eta_days' => 3,
            ],
            [
                'key' => 'fulfillment_pickup',
                'label_he' => 'איסוף עצמי',
                'tag' => 'stage-fulfillment-pickup',
                'position' => 10,
                'fulfillment_choice' => 'pickup',
            ],
            [
                'key' => 'fulfillment_delivery',
                'label_he' => 'משלוח עד הבית',
                'tag' => 'stage-fulfillment-delivery',
                'position' => 11,
                'fulfillment_choice' => 'delivery',
            ],
            [
                'key' => 'fulfillment_install',
                'label_he' => 'משלוח והתקנה',
                'tag' => 'stage-fulfillment-install',
                'position' => 12,
                'fulfillment_choice' => 'delivery_installation',
            ],
        ]);

        $service = new OrderProgressService();
        $shop = new User();

        return $service->mergedChecklistTemplatesForOrder($shop, [
            'id' => 1,
            'tags' => $tags,
            'line_items' => [],
        ]);
    }

    public function test_without_flow_tag_keeps_all_fulfillment_rows(): void
    {
        $keys = array_column($this->templatesForOrderTags('stage-suppliers-ordered'), 'key');

        $this->assertContains('suppliers_ordered', $keys);
        $this->assertContains('fulfillment_pickup', $keys);
        $this->assertContains('fulfillment_delivery', $keys);
        $this->assertContains('fulfillment_install', $keys);
    }

    public function test_delivery_flow_tag_keeps_only_delivery_fulfillment_row(): void
    {
        $keys = array_column($this->templatesForOrderTags('משלוח עד הבית'), 'key');

        $this->assertContains('suppliers_ordered', $keys);
        $this->assertContains('fulfillment_delivery', $keys);
        $this->assertNotContains('fulfillment_pickup', $keys);
        $this->assertNotContains('fulfillment_install', $keys);
    }

    public function test_pickup_flow_tag_keeps_only_pickup_fulfillment_row(): void
    {
        $keys = array_column($this->templatesForOrderTags('אריה שנקר 26'), 'key');

        $this->assertContains('fulfillment_pickup', $keys);
        $this->assertNotContains('fulfillment_delivery', $keys);
        $this->assertNotContains('fulfillment_install', $keys);
    }

    public function test_install_flow_tag_keeps_only_install_fulfillment_row(): void
    {
        $keys = array_column($this->templatesForOrderTags('משלוח + התקנה'), 'key');

        $this->assertContains('fulfillment_install', $keys);
        $this->assertNotContains('fulfillment_pickup', $keys);
        $this->assertNotContains('fulfillment_delivery', $keys);
    }

    public function test_filter_disabled_keeps_all_rows_even_with_flow_tag(): void
    {
        $keys = array_column($this->templatesForOrderTags('משלוח עד הבית', false), 'key');

        $this->assertContains('fulfillment_pickup', $keys);
        $this->assertContains('fulfillment_delivery', $keys);
        $this->assertContains('fulfillment_install', $keys);
    }

    public function test_steps_without_fulfillment_choice_are_never_removed(): void
    {
        Config::set('order-progress.use_product_metafield_checklist', false);
        Config::set('order-progress.filter_fulfillment_options_by_flow_tag', true);
        Config::set('order-progress.fulfillment_choice_tags', [
            'delivery' => 'משלוח עד הבית',
        ]);
        Config::set('order-progress.steps', [
            [
                'key' => 'only_production',
                'label_he' => 'שלב ייצור',
                'tag' => 'stage-only',
                'position' => 0,
            ],
        ]);

        $service = new OrderProgressService();
        $rows = $service->mergedChecklistTemplatesForOrder(new User(), [
            'id' => 1,
            'tags' => 'משלוח עד הבית',
            'line_items' => [],
        ]);

        $this->assertCount(1, $rows);
        $this->assertSame('only_production', $rows[0]['key']);
    }
}
