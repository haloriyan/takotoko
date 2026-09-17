<?php

namespace App\Http\Controllers;

use App\Models\CmsCategory;
use App\Models\CmsContent;
use App\Models\Sales;
use App\Models\User;
use App\Services\Tripay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function pay(Tripay $tripay) {
        return env('BASE_URL') . "/api/callback/tripay";
        // $signature = $tripay->signature([
        //     'amount' => 15000,
        //     'merchant_ref' => "INV_123"
        // ]);

        // $pay = $tripay->pay([
        //     'method' => "QRIS",
        //     'merchant_ref' => "INV_123",
        //     'amount' => 15000,
        //     'customer_name' => "Riyan Satria",
        //     'customer_email' => "riyan@gmail.com",
        //     'signature' => $signature,
        //     'order_items' => [
        //         [
        //             'sku'         => 'O-06',
        //             'name'        => 'Oreo',
        //             'price'       => 15000,
        //             'quantity'    => 1,
        //             // 'product_url' => 'https://tokokamu.com/product/nama-produk-1',
        //             // 'image_url'   => 'https://tokokamu.com/product/nama-produk-1.jpg',
        //         ],
        //     ]
        // ]);

        // Log::info(
        //     json_encode($pay, JSON_PRETTY_PRINT)
        // );

        return $pay;
    }
    public function index() {
        return view('index');
    }
    public function about() {
        return view('about');
    }
    public function contact() {
        return view('contact');
    }
    public function privacyPolicy() {
        return view('about.privacy');
    }
    public function pricing() {
        $plans = config('plans');
        return view('pricing', [
            'plans' => $plans,
        ]);
    }

    public function caseWarkop() {
        return view('case.warkop');
    }
    public function caseWarmad() {
        return view('case.warmad');
    }

    public function blog(Request $request) {
        $categories = CmsCategory::orderBy('name', 'ASC')
        ->with([
            'posts' => function ($query) {
                $query->inRandomOrder()->take(15);
            }
        ])
        ->get();

        $featured = CmsContent::inRandomOrder()->take(5)->get();
        $sections = CmsCategory::inRandomOrder()->take(5)
        ->with([
            'posts' => function ($query) {
                $query->inRandomOrder()->take(15);
            }
        ])
        ->get();

        if ($request->api == 1) {
            return response()->json([
                'categories' => $categories,
                'featured' => $featured,
                'sections' => $sections,
            ]);
        }

        return view('blog.index', [
            'categories' => $categories,
            'featured' => $featured,
            'sections' => $sections,
        ]);
    }
    public function blogRead(Request $request, $slug) {
        $post = CmsContent::where('slug', $slug)->with(['slides','categories'])->first();
        $related = CmsContent::whereHas('categories', function ($query) use ($post) {
            $query->whereIn('category_id', $post->categories->pluck('id'));
        })
        ->take(10)
        ->inRandomOrder()
        ->get();
        
        $sections = CmsCategory::inRandomOrder()->take(3)
        ->with([
            'posts' => function ($query) {
                $query->inRandomOrder()->take(15);
            }
        ])
        ->get();

        if ($request->api == 1) {
            return response()->json([
                'post' => $post,
                'related' => $related,
                'sections' => $sections,
            ]);
        }

        return view('blog.read', [
            'post' => $post,
            'related' => $related,
            'sections' => $sections,
        ]);
    }
    public function blogCategory(Request $request, $slug) {
        $category = CmsCategory::where('slug', $slug)->first();
        $posts = CmsContent::whereHas('categories', function ($query) use ($category) {
            $query->where('category_id', $category->id);
        })
        ->paginate(12);
        
        $categories = CmsCategory::orderBy('name', 'ASC')
        ->get();

        if ($request->api == 1) {
            return response()->json([
                'categories' => $categories,
                'category' => $category,
                'posts' => $posts,
            ]);
        }

        return view('blog.category', [
            'categories' => $categories,
            'category' => $category,
            'posts' => $posts,
        ]);
    }
    public function faq(Request $request) {
        $topics = [
            'Umum', 'Produk', 'Transaksi & Pembayaran',
        ];
        $faqs = [
            [
                'question' => "Jika ditanya begini",
                'answer' => "Jawabannya begitu"
            ],
            [
                'question' => "Kalau ditanya begitu",
                'answer' => "Jawabannya harus begini"
            ],
        ];
        // Str::slug('h',);

        return view('faq', [
            'topics' => $topics,
            'request' => $request,
            'faqs' => json_decode(json_encode($faqs), false)
        ]);
    }
    public function panduan() {
        return view('panduan');
    }
    public function deleteAccount(Request $request) {
        if ($request->isMethod('POST')) {
            $email = $request->email;
            $user = User::where('email', $email)->first();

            if ($user == null) {
                return redirect()->back()->withErrors([
                    'Kami tidak dapat menemukan akun Anda dengan email ' . $email,
                ]);
            } else {
                return redirect()->route('delAccount')->with([
                    'message' => "Permintaan Anda telah Kami terima. Akun Anda akan terhapus beserta data terkait dalam waktu 1 x 24 jam."
                ]);
            }
        }

        $message = Session::get('message');
        return view('delete_account', [
            'message' => $message,
        ]);
    }
    public function receipt(Request $request, $invoiceNumber) {
        $sales = Sales::where('invoice_number', $invoiceNumber)
        ->with(['store', 'customer', 'items.product.images', 'review'])
        ->first();
        $message = Session::get('message');

        $customerID = $request->customer_id;

        if ($customerID != $sales->customer_id) {
            return "error";
        }

        return view('receipt', [
            'sales' => $sales,
            'message' => $message,
        ]);
    }
}
