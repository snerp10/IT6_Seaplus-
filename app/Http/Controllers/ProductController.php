namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category', 'Sand');
        $products = Product::where('category', $category)
                         ->where('stock_quantity', '>', 0)
                         ->get();
        return view('products.index', compact('products', 'category'));
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
