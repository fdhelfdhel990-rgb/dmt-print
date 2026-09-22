<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderFile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()->with('customer', 'items')->when($request->string('q')->isNotEmpty(), fn ($q) => $q->where(fn ($q) => $q->where('order_number', 'like', '%'.$request->string('q').'%')->orWhereHas('customer', fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%')->orWhere('phone', 'like', '%'.$request->string('q').'%'))))->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))->when($request->filled('fulfillment_method'), fn ($q) => $q->where('fulfillment_method', $request->string('fulfillment_method')))->latest()->paginate(15)->withQueryString();

        return view('admin.orders-dynamic', compact('orders'));
    }

    public function show(Order $order): View
    {
        return view('admin.order-detail-dynamic', ['order' => $order->load('customer', 'items.options', 'items.files', 'statusHistories.user')]);
    }

    public function download(OrderFile $file): StreamedResponse
    {
        return Storage::disk($file->disk)->download($file->path, $file->original_name);
    }
}
