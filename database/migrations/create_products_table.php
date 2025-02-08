use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('name');
            $table->enum('category', ['Sand', 'Gravel', 'Hollow Blocks', 'Hardware Supplies']);
            $table->decimal('price', 8, 2);
            $table->string('unit_of_measurement');
            $table->integer('stock_quantity');
            $table->unsignedBigInteger('supplier_id');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
