<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDashboardViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create Sales Analytics View
        DB::statement("
            CREATE VIEW vw_sales_analytics AS
            SELECT 
                DATE(o.created_at) as sale_date,
                COUNT(DISTINCT o.order_id) as order_count,
                SUM(o.total_amount) as revenue,
                SUM(od.quantity) as items_sold,
                p.category as product_category
            FROM orders o
            JOIN order_details od ON o.order_id = od.order_id
            JOIN products p ON od.prod_id = p.prod_id
            WHERE o.order_status = 'Completed'
            GROUP BY DATE(o.created_at), p.category
            ORDER BY sale_date DESC
        ");
        
        // Create Inventory Analytics View
        DB::statement("
            CREATE VIEW vw_inventory_analytics AS
            SELECT 
                p.prod_id,
                p.name as product_name,
                p.category,
                i.curr_stock as current_stock,
                i.move_date as last_updated,
                CASE 
                    WHEN i.curr_stock <= 10 THEN 'Low'
                    WHEN i.curr_stock <= 20 THEN 'Medium'
                    ELSE 'Good'
                END as stock_status,
                COUNT(od.detail_id) as times_ordered
            FROM products p
            LEFT JOIN inventories i ON p.prod_id = i.prod_id AND i.inv_id = (
                SELECT MAX(inv_id) FROM inventories WHERE prod_id = p.prod_id
            )
            LEFT JOIN order_details od ON p.prod_id = od.prod_id
            GROUP BY p.prod_id, p.name, p.category, i.curr_stock, i.move_date
            ORDER BY times_ordered DESC
        ");
        
        // Create trigger for order creation
        DB::unprepared("
            CREATE TRIGGER trg_after_order_complete
            AFTER UPDATE ON orders
            FOR EACH ROW
            BEGIN
                IF NEW.order_status = 'Completed' AND OLD.order_status != 'Completed' THEN
                    INSERT INTO notifications (title, message, type, created_at, updated_at) 
                    VALUES (
                        'Order Completed', 
                        CONCAT('Order #', NEW.order_id, ' has been completed'), 
                        'order_complete',
                        NOW(),
                        NOW()
                    );
                END IF;
            END
        ");
        
        // Create trigger for low stock notification
        DB::unprepared("
            CREATE TRIGGER trg_after_inventory_update
            AFTER UPDATE ON inventories
            FOR EACH ROW
            BEGIN
                IF NEW.curr_stock <= 10 AND (OLD.curr_stock > 10 OR OLD.curr_stock IS NULL) THEN
                    INSERT INTO notifications (title, message, type, created_at, updated_at) 
                    SELECT 
                        'Low Stock Alert', 
                        CONCAT(p.name, ' is running low (', NEW.curr_stock, ' left)'),
                        'low_stock',
                        NOW(),
                        NOW()
                    FROM products p 
                    WHERE p.prod_id = NEW.prod_id;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop triggers
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_order_complete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_inventory_update');
        
        // Drop views
        DB::statement('DROP VIEW IF EXISTS vw_sales_analytics');
        DB::statement('DROP VIEW IF EXISTS vw_inventory_analytics');
    }
}
