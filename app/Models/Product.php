namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'category',
        'price',
        'unit_of_measurement',
        'stock_quantity',
        'supplier_id',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function getCategoryProducts($category)
    {
        return $this->where('category', $category)->get();
    }
}
