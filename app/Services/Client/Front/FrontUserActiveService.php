<?php

namespace App\Services\Client\Front;

use App\Components\Transport\Protokol\Http\HttpClientInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;

class FrontUserActiveService
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function addToCart(): void
    {
        $prev_name = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        if ($prev_name == 'client.products.filter') session(['backFilter' => true]);
        foreach (request('addToCart') as $product_type_id => $amount) {
            empty($amount) ? session()->forget('cart.' . $product_type_id) : session(['cart.' . $product_type_id => $amount]);
        }
    }

    public function likedToggle(int $product_type_id): void
    {
        $prev_name = Route::getRoutes()->match(request()->create(url()->previousPath()))->getName();
        if ($prev_name == 'client.products.filter') session(['backFilter' => true]);
        $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.likedToggle', $product_type_id, false))
            ->setMethod('POST')
            ->publish();
    }

    /**
     * @param array<mixed> $data
     * @return void
     */
    public function commentStore(array $data): void
    {
        if (!empty($data['comment_images'])) $this->imgEncode($data['comment_images']);
        $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.products.commentStore', $data['product_id'], false))
            ->setQuery($data)
            ->setMethod('POST')
            ->publish();
    }

    /**
     * @param array<UploadedFile> &$images
     * @return void
     */
    private function imgEncode(array &$images): void
    {
        foreach ($images as &$img) {
            $img = [
                'path' => $img->getPathname(),
                'originalName' => $img->getClientOriginalName(),
                'mimeType' => $img->getClientMimeType(),
            ];
        }
    }
}
