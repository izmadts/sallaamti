<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;


class SubscriberAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscriber::query();
        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'verified':
                    $query->whereNotNull('verified_at');
                    break;
                case 'pending':
                    $query->whereNull('verified_at');
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }
        $subscribers = $query->latest()->paginate(20)->withQueryString();
        return view('admin.subscribers.index', [
            'subscribers' => $subscribers,
            'stats' => [
                'total' => Subscriber::count(),
                'verified' => Subscriber::whereNotNull('verified_at')->count(),
                'pending' => Subscriber::whereNull('verified_at')->count(),
                'inactive' => Subscriber::where('is_active', false)->count(),
            ]
        ]);
    }
    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'Subscriber deleted successfully.');
    }
}
