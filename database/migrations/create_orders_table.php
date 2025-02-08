use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('customer_id');
            $table->date('order_date');
            $table->decimal('total_amount', 8, 2);
            $table->enum('payment_method', ['Cash', 'GCash']);
            $table->enum('payment_status', ['Paid', 'Unpaid']);
            $table->enum('order_type', ['Retail', 'Bulk']);
            $table->enum('delivery_status', ['Pending', 'Delivered']);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
