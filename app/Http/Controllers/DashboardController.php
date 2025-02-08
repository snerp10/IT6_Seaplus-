namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $recentOrders = Order::where('customer_id', auth()->id())
                            ->latest()
                            ->take(5)
                            ->get();
        
        $categories = ['Sand', 'Gravel', 'Hollow Blocks', 'Hardware Supplies'];
        
        return view('dashboard.index', compact('recentOrders', 'categories'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('dashboard.profile', compact('user'));
    }
}
