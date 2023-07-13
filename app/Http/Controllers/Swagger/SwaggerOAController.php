<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="Back API doc",
 *     version="1.0",
 * ),
 *
 * @OA\PathItem(
 *     path="/api/",
 * ),
 *
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/cart",
 *     summary="просмотр корзины",
 *     tags={"cart"},
 *
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="cart", type="object",
 *                 @OA\Property(property=443224, type="integer", example=1),
 *             ),
 *         ),
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example="1"),
 *             @OA\Property(property="product_id", type="integer", example="1"),
 *             @OA\Property(property="price", type="integer", example="300"),
 *             @OA\Property(property="count", type="integer", example="5"),
 *             @OA\Property(property="is_published", type="bool", example=true),
 *             @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *             @OA\Property(property="option_values", type="object",
 *                 @OA\Property(property="Цвет", type="string", example="красный"),
 *                 @OA\Property(property="Размер", type="string", example="300 грамм")
 *             ),
 *             @OA\Property(property="category", type="string", example="chokolate"),
 *             @OA\Property(property="title", type="string", example="Cool chokolate"),
 *             @OA\Property(property="amount", type="integer", example="2"),
 *             @OA\Property(property="totalPrice", type="integer", example="600"),
 *         ),
 *     ),
 * ),
 *
 *
 * @OA\Post(
 *     path="/api/orders",
 *     summary="просмотр заказов",
 *     tags={"orders"},
 *     security={{ "bearerAuth": {} }},
 *
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="page", type="integer", example=1),
 *         ),
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *         @OA\JsonContent(
 *             @OA\Property(property="current_page", type="integer", example="1"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example="1"),
 *                     @OA\Property(property="productTypes", type="array",
 *                         @OA\Items(type="object",
 *                             @OA\Property(property="id", type="integer", example="1"),
 *                             @OA\Property(property="amount", type="integer", example="1"),
 *                             @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                             @OA\Property(property="category", type="string", example="chokolate"),
 *                         ),
 *                     ),
 *                     @OA\Property(property="delivery", type="string", example="post. Получатель: Lebsack Clotilde Parker Cecelia DuBuque. Адрес: 522 Renner Isle\nLake Demarco, ME 81738-5083"),
 *                     @OA\Property(property="total_price", type="integer", example="5000"),
 *                     @OA\Property(property="status", type="string", example="В работе"),
 *                     @OA\Property(property="created_at", format="date", example="2023-07-03"),
 *                     @OA\Property(property="dispatch_time", format="date", example="2023-07-25"),
 *                 ),
 *             ),
 *             @OA\Property(property="first_page_url", type="integer", example="1"),
 *             @OA\Property(property="from", type="integer", example="1"),
 *             @OA\Property(property="next_page_url", type="integer|null", example="null"),
 *             @OA\Property(property="path", type="string", example=""),
 *             @OA\Property(property="per_page", type="integer", example="8"),
 *             @OA\Property(property="prev_page_url", type="integer|null", example="null"),
 *             @OA\Property(property="to", type="integer", example="1"),
 *         ),
 *     ),
 * ),
 *
 *
 * @OA\Post(
 *     path="/api/orders/store",
 *     summary="создание заказа",
 *     tags={"orders"},
 *     security={{ "bearerAuth": {} }},
 *
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="total_price", type="integer", example=1000),
 *             @OA\Property(property="payment_status", type="bool", example=true),
 *             @OA\Property(property="payment", type="string", example="card"),
 *             @OA\Property(property="cart", type="object",
 *                 @OA\Property(property="662341", type="integer", example="2"),
 *                 @OA\Property(property="115521", type="integer", example="3"),
 *             ),
 *         ),
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *     ),
 * ),
 *
 *
 * @OA\Post(
 *     path="/api/orders/{order}",
 *     summary="просмотр заказа",
 *     tags={"orders"},
 *     security={{ "bearerAuth": {} }},
 *
 *     @OA\Parameter(
 *         description="ID заказа",
 *         in="path",
 *         name="order",
 *         required=true,
 *         example=1,
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example="1"),
 *             @OA\Property(property="productTypes", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example="1"),
 *                     @OA\Property(property="amount", type="integer", example="3"),
 *                     @OA\Property(property="price", type="integer", example="400"),
 *                     @OA\Property(property="optionValues", type="object",
 *                         @OA\Property(property="Цвет", type="string", example="красный"),
 *                         @OA\Property(property="Размер", type="string", example="300 грамм"),
 *                     ),
 *                     @OA\Property(property="title", type="string", example="Cool chokolate"),
 *                     @OA\Property(property="saler_id", type="integer", example="1"),
 *                     @OA\Property(property="saler", type="string", example="Ivan Ivanov"),
 *                     @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                     @OA\Property(property="category", type="string", example="chokolate"),
 *                     @OA\Property(property="status", type="string", example="Отправлен 2023-07-03 10:55:50"),
 *                     @OA\Property(property="orderPerformer_id", type="integer", example="1"),
 *                 ),
 *             ),
 *             @OA\Property(property="delivery", type="string", example="post. Получатель: Lebsack Clotilde Parker Cecelia DuBuque. Адрес: 522 Renner Isle\nLake Demarco, ME 81738-5083"),
 *             @OA\Property(property="total_price", type="integer", example="5000"),
 *             @OA\Property(property="status", type="string", example="В работе"),
 *             @OA\Property(property="created_at", format="date", example="2023-07-03"),
 *             @OA\Property(property="dispatch_time", format="date", example="2023-07-25"),
 *         ),
 *     ),
 * ),
 *
 * @OA\Patch(
 *     path="/api/orders/{order}",
 *     summary="отметка о получении заказа",
 *     tags={"orders"},
 *     security={{ "bearerAuth": {} }},
 *
 *     @OA\Parameter(
 *         description="ID заказа",
 *         in="path",
 *         name="order",
 *         required=true,
 *         example=1,
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *     ),
 * ),
 *
 *  @OA\Delete(
 *     path="/api/orders/{order}",
 *     summary="Отмена заказа",
 *     tags={"orders"},
 *     security={{ "bearerAuth": {} }},
 *
 *     @OA\Parameter(
 *         description="ID заказа",
 *         in="path",
 *         name="order",
 *         required=true,
 *         example=1,
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *     ),
 * ),
 *
 * @OA\Post(
 *     path="/api/products",
 *     summary="главная страница",
 *     tags={"products"},
 *
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="viewed", type="object", example={1, 2}),
 *         ),
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *         @OA\JsonContent(
 *             @OA\Property(property="liked_ids", type="object",
 *                 @OA\Property(property="662341", type="integer", example="1"),
 *                 @OA\Property(property="115521", type="integer", example="2"),
 *             ),
 *             @OA\Property(property="liked", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example="1"),
 *                     @OA\Property(property="product_id", type="integer", example="1"),
 *                     @OA\Property(property="price", type="integer", example="100"),
 *                     @OA\Property(property="count", type="integer", example="5"),
 *                     @OA\Property(property="is_published", type="bool", example="true"),
 *                     @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                     @OA\Property(property="product_images", type="object", example={"product_images/1.jpg", "product_images/2.jpg"}),
 *                     @OA\Property(property="option_values", type="object",
 *                         @OA\Property(property="Цвет", type="array",
 *                             @OA\Items(type="object",
 *                                 @OA\Property(property="id", type="integer", example="1"),
 *                                 @OA\Property(property="option_id", type="integer", example="1"),
 *                                 @OA\Property(property="value", type="string", example="красный"),
 *                                 @OA\Property(property="created_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                                 @OA\Property(property="updated_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                             ),
 *                         ),
 *                     ),
 *                     @OA\Property(property="product", type="object",
 *                         @OA\Property(property="id", type="integer", example="1"),
 *                         @OA\Property(property="title", type="string", example="cool chokolate"),
 *                         @OA\Property(property="category", type="object",
 *                             @OA\Property(property="id", type="integer", example="1"),
 *                             @OA\Property(property="title", type="string", example="chokolate"),
 *                         ),
 *                         @OA\Property(property="rating", type="integer", example="5"),
 *                         @OA\Property(property="countRating", type="integer", example="1"),
 *                         @OA\Property(property="countComments", type="integer", example="1"),
 *                         @OA\Property(property="product_types", type="array",
 *                             @OA\Items(type="object",
 *                                 @OA\Property(property="id", type="integer", example="1"),
 *                                 @OA\Property(property="product_id", type="integer", example="1"),
 *                                 @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                                 @OA\Property(property="is_published", type="bool", example="true"),
 *                             ),
 *                         ),
 *                     ),
 *                 ),
 *             ),
 *             @OA\Property(property="viewed", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example="1"),
 *                     @OA\Property(property="product_id", type="integer", example="1"),
 *                     @OA\Property(property="price", type="integer", example="100"),
 *                     @OA\Property(property="count", type="integer", example="5"),
 *                     @OA\Property(property="is_published", type="bool", example="true"),
 *                     @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                     @OA\Property(property="product_images", type="object", example={"product_images/1.jpg", "product_images/2.jpg"}),
 *                     @OA\Property(property="option_values", type="object",
 *                         @OA\Property(property="Цвет", type="array",
 *                             @OA\Items(type="object",
 *                                 @OA\Property(property="id", type="integer", example="1"),
 *                                 @OA\Property(property="option_id", type="integer", example="1"),
 *                                 @OA\Property(property="value", type="string", example="красный"),
 *                                 @OA\Property(property="created_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                                 @OA\Property(property="updated_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                             ),
 *                         ),
 *                     ),
 *                     @OA\Property(property="product", type="object",
 *                         @OA\Property(property="id", type="integer", example="1"),
 *                         @OA\Property(property="title", type="string", example="cool chokolate"),
 *                         @OA\Property(property="category", type="object",
 *                             @OA\Property(property="id", type="integer", example="1"),
 *                             @OA\Property(property="title", type="string", example="chokolate"),
 *                         ),
 *                         @OA\Property(property="rating", type="integer", example="5"),
 *                         @OA\Property(property="countRating", type="integer", example="1"),
 *                         @OA\Property(property="countComments", type="integer", example="1"),
 *                         @OA\Property(property="product_types", type="array",
 *                             @OA\Items(type="object",
 *                                 @OA\Property(property="id", type="integer", example="1"),
 *                                 @OA\Property(property="product_id", type="integer", example="1"),
 *                                 @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                                 @OA\Property(property="is_published", type="bool", example="true"),
 *                             ),
 *                         ),
 *                     ),
 *                 ),
 *             ),
 *         ),
 *     ),
 * ),
 *
 * @OA\Post(
 *     path="/api/products/{category}",
 *     summary="просмотр товаров по категории",
 *     tags={"products"},
 *
 *     @OA\Parameter(
 *         description="title категории",
 *         in="path",
 *         name="category",
 *         required=true,
 *         example="chokolate",
 *     ),
 *
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="filter", type="object",
 *                 @OA\Property(property="tags", type="object", example={1,2}),
 *                 @OA\Property(property="salers", type="object", example={1,2}),
 *                 @OA\Property(property="optionValues", type="object",
 *                       @OA\Property(property="1", type="object", example={1,2}),
 *                       @OA\Property(property="2", type="object", example={6,7}),
 *                 ),
 *                 @OA\Property(property="prices", type="object",
 *                     @OA\Property(property="min", type="integer", example="1000"),
 *                     @OA\Property(property="max", type="integer", example="9000"),
 *                 ),
 *                 @OA\Property(property="search", type="string", example="cool chokolate"),
 *             ),
 *             @OA\Property(property="paginate", type="object",
 *                 @OA\Property(property="orderBy", type="string", example="rating"),
 *                 @OA\Property(property="perPage", type="integer", example="8"),
 *                 @OA\Property(property="page", type="integer", example="1"),
 *             ),
 *             @OA\Property(property="cart", type="object",
 *                 @OA\Property(property="662341", type="integer", example="2"),
 *                 @OA\Property(property="115521", type="integer", example="3"),
 *             ),
 *         ),
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *         @OA\JsonContent(
 *             @OA\Property(property="productTypes", type="object",
 *                 @OA\Property(property="current_page", type="integer", example="1"),
 *                 @OA\Property(property="data", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example="1"),
 *                         @OA\Property(property="product_id", type="integer", example="1"),
 *                         @OA\Property(property="price", type="integer", example="100"),
 *                         @OA\Property(property="count", type="integer", example="5"),
 *                         @OA\Property(property="is_published", type="bool", example="true"),
 *                         @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                         @OA\Property(property="product_images", type="object", example={"product_images/1.jpg", "product_images/2.jpg"}),
 *                         @OA\Property(property="option_values", type="object",
 *                             @OA\Property(property="Цвет", type="array",
 *                                 @OA\Items(type="object",
 *                                     @OA\Property(property="id", type="integer", example="1"),
 *                                     @OA\Property(property="option_id", type="integer", example="1"),
 *                                     @OA\Property(property="value", type="string", example="красный"),
 *                                     @OA\Property(property="created_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                                     @OA\Property(property="updated_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                                 ),
 *                             ),
 *                         ),
 *                         @OA\Property(property="product", type="object",
 *                             @OA\Property(property="id", type="integer", example="1"),
 *                             @OA\Property(property="title", type="string", example="cool chokolate"),
 *                             @OA\Property(property="category", type="string", example=""),
 *                             @OA\Property(property="rating", type="integer", example="5"),
 *                             @OA\Property(property="countRating", type="integer", example="1"),
 *                             @OA\Property(property="countComments", type="integer", example="1"),
 *                             @OA\Property(property="product_types", type="array",
 *                                 @OA\Items(type="object",
 *                                     @OA\Property(property="id", type="integer", example="1"),
 *                                     @OA\Property(property="product_id", type="integer", example="1"),
 *                                     @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                                     @OA\Property(property="is_published", type="bool", example="true"),
 *                                 ),
 *                             ),
 *                         ),
 *                     ),
 *                 ),
 *                 @OA\Property(property="first_page_url", type="integer", example="1"),
 *                 @OA\Property(property="from", type="integer", example="1"),
 *                 @OA\Property(property="next_page_url", type="integer|null", example="null"),
 *                 @OA\Property(property="path", type="string", example=""),
 *                 @OA\Property(property="per_page", type="integer", example="8"),
 *                 @OA\Property(property="prev_page_url", type="integer|null", example="null"),
 *                 @OA\Property(property="to", type="integer", example="1"),
 *             ),
 *             @OA\Property(property="paginate", type="object",
 *                 @OA\Property(property="orderBy", type="string", example="rating"),
 *                 @OA\Property(property="perPage", type="integer", example="8"),
 *                 @OA\Property(property="page", type="integer", example="1"),
 *             ),
 *             @OA\Property(property="filter", type="object",
 *                 @OA\Property(property="tags", type="object", example={1,2}),
 *                 @OA\Property(property="salers", type="object", example={1,2}),
 *                 @OA\Property(property="optionValues", type="object", example={1,2}),
 *                 @OA\Property(property="propertyValues", type="object", example={1,2}),
 *                 @OA\Property(property="prices", type="object",
 *                     @OA\Property(property="min", type="integer", example="100"),
 *                     @OA\Property(property="max", type="integer", example="1000"),
 *                 ),
 *                 @OA\Property(property="search", type="string", example="cool chokolate"),
 *             ),
 *             @OA\Property(property="filterable", type="object",
 *                 @OA\Property(property="tags", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="title", type="string", example="some tag"),
 *                     ),
 *                 ),
 *                 @OA\Property(property="salers", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Best goods"),
 *                     ),
 *                 ),
 *                 @OA\Property(property="optionValues", type="object",
 *                       @OA\Property(property="Цвет", type="array",
 *                          @OA\Items(type="object",
 *                              @OA\Property(property="id", type="integer", example=1),
 *                              @OA\Property(property="option_id", type="integer", example=1),
 *                              @OA\Property(property="value", type="string", example="Белый"),
 *                          ),
 *                      ),
 *                 ),
 *                 @OA\Property(property="propertyValues", type="object",
 *                      @OA\Property(property="Упаковка", type="array",
 *                          @OA\Items(type="object",
 *                              @OA\Property(property="id", type="integer", example=1),
 *                              @OA\Property(property="property_id", type="integer", example=1),
 *                              @OA\Property(property="value", type="string", example="Коробка"),
 *                          ),
 *                      ),
 *                 ),
 *                 @OA\Property(property="prices", type="object",
 *                     @OA\Property(property="min", type="integer", example="100"),
 *                     @OA\Property(property="max", type="integer", example="1000"),
 *                 ),
 *             ),
 *             @OA\Property(property="category", type="object",
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="title", type="string", example="chokolate"),
 *                 @OA\Property(property="title_rus", type="string", example="Шоколад ручной работы"),
 *                 @OA\Property(property="created_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                 @OA\Property(property="updated_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *             ),
 *             @OA\Property(property="liked_ids", type="object",
 *                 @OA\Property(property="662341", type="integer", example="2"),
 *                 @OA\Property(property="115521", type="integer", example="3"),
 *             ),
 *         ),
 *     ),
 * ),
 *
 * @OA\Post(
 *     path="/api/products/liked",
 *     summary="просмотр понравившихся товаров",
 *     tags={"products"},
 *     security={{ "bearerAuth": {} }},
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example="1"),
 *             @OA\Property(property="product_id", type="integer", example="1"),
 *             @OA\Property(property="price", type="integer", example="100"),
 *             @OA\Property(property="count", type="integer", example="5"),
 *             @OA\Property(property="is_published", type="bool", example="true"),
 *             @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *             @OA\Property(property="product_images", type="object", example={"product_images/1.jpg", "product_images/2.jpg"}),
 *             @OA\Property(property="option_values", type="object",
 *                 @OA\Property(property="Цвет", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example="1"),
 *                         @OA\Property(property="option_id", type="integer", example="1"),
 *                         @OA\Property(property="value", type="string", example="красный"),
 *                         @OA\Property(property="created_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                         @OA\Property(property="updated_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                     ),
 *                 ),
 *             ),
 *             @OA\Property(property="product", type="object",
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="title", type="string", example="cool chokolate"),
 *                 @OA\Property(property="category", type="object",
 *                     @OA\Property(property="id", type="integer", example="1"),
 *                     @OA\Property(property="title", type="string", example="chokolate"),
 *                 ),
 *                 @OA\Property(property="rating", type="integer", example="5"),
 *                 @OA\Property(property="countRating", type="integer", example="1"),
 *                 @OA\Property(property="countComments", type="integer", example="1"),
 *                 @OA\Property(property="product_types", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example="1"),
 *                         @OA\Property(property="product_id", type="integer", example="1"),
 *                         @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                         @OA\Property(property="is_published", type="bool", example="true"),
 *                     ),
 *                 ),
 *             ),
 *         ),
 *     ),
 * ),
 *
 * @OA\Post(
 *     path="/api/products/liked/{productType}/toggle",
 *     summary="добавление (исключение) понравившегося товара",
 *     tags={"products"},
 *     security={{ "bearerAuth": {} }},
 *
 *     @OA\Parameter(
 *         description="ID типа продукта",
 *         in="path",
 *         name="productType",
 *         required=true,
 *         example=1,
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *     ),
 * ),
 *
 * @OA\Post(
 *     path="/api/products/{category}/{productType}",
 *     summary="просмотр товара",
 *     tags={"products"},
 *
 *     @OA\Parameter(
 *         description="title категории",
 *         in="path",
 *         name="category",
 *         required=true,
 *         example="chokolate",
 *     ),
 *     @OA\Parameter(
 *         description="ID типа продукта",
 *         in="path",
 *         name="productType",
 *         required=true,
 *         example=1,
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example="1"),
 *             @OA\Property(property="product_id", type="integer", example="1"),
 *             @OA\Property(property="price", type="integer", example="100"),
 *             @OA\Property(property="count", type="integer", example="5"),
 *             @OA\Property(property="is_published", type="bool", example="true"),
 *             @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *             @OA\Property(property="product_images", type="object", example={"product_images/1.jpg", "product_images/2.jpg"}),
 *             @OA\Property(property="option_values", type="object",
 *                 @OA\Property(property="Цвет", type="string", example="Красный"),
 *                 @OA\Property(property="Упаковка", type="string", example="Коробка"),
 *             ),
 *             @OA\Property(property="likeable", type="bool", example="true"),
 *             @OA\Property(property="product", type="object",
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="title", type="string", example="cool chokolate"),
 *                 @OA\Property(property="description", type="string", example="It's very cool chokolate"),
 *                 @OA\Property(property="saler_id", type="integer", example="1"),
 *                 @OA\Property(property="saler", type="string", example="Ivanov Ivan"),
 *                 @OA\Property(property="option_values", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example="1"),
 *                         @OA\Property(property="option_id", type="integer", example="1"),
 *                         @OA\Property(property="value", type="string", example="красный"),
 *                         @OA\Property(property="created_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                         @OA\Property(property="updated_at", format="date", example="2023-06-28T12:03:41.000000Z"),
 *                         @OA\Property(property="option", type="object",
 *                             @OA\Property(property="id", type="integer", example="1"),
 *                             @OA\Property(property="title", type="string", example="Цвет"),
 *                         ),
 *                     ),
 *                 ),
 *                 @OA\Property(property="category", type="object",
 *                     @OA\Property(property="id", type="integer", example="1"),
 *                     @OA\Property(property="title", type="string", example="chokolate"),
 *                     @OA\Property(property="title_rus", type="string", example="Шоколад ручной работы"),
 *                 ),
 *                 @OA\Property(property="property_values", type="object",
 *                     @OA\Property(property="Высота", type="string", example="10 см"),
 *                     @OA\Property(property="Ширина", type="string", example="5 см"),
 *                 ),
 *                 @OA\Property(property="product_types", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example="1"),
 *                         @OA\Property(property="product_id", type="integer", example="1"),
 *                         @OA\Property(property="preview_image", type="string", example="preview_images/1.jpg"),
 *                         @OA\Property(property="is_published", type="bool", example="true"),
 *                     ),
 *                 ),
 *                 @OA\Property(property="rating", type="integer", example="5"),
 *                 @OA\Property(property="countRating", type="integer", example="1"),
 *                 @OA\Property(property="countComments", type="integer", example="1"),
 *                 @OA\Property(property="commentable", type="bool", example="true"),
 *                 @OA\Property(property="ratingAndComments", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="message", type="string", example="so sweety"),
 *                         @OA\Property(property="rating", type="integer", example="5"),
 *                         @OA\Property(property="user", type="object",
 *                             @OA\Property(property="id", type="integer", example="1"),
 *                             @OA\Property(property="name", type="string", example="Petrov Petr"),
 *                         ),
 *                         @OA\Property(property="commentImages", type="object", example={"comments/1.jpg", "comments/2.jpg"}),
 *                         @OA\Property(property="created_at", type="string", example="2 hours ago"),
 *                     ),
 *                 ),
 *             ),
 *         ),
 *     ),
 * ),
 *
 * @OA\Post(
 *     path="/api/products/{product}/comment",
 *     summary="добавление комментария",
 *     tags={"products"},
 *     security={{ "bearerAuth": {} }},
 *
 *     @OA\Parameter(
 *         description="ID продукта",
 *         in="path",
 *         name="product",
 *         required=true,
 *         example=1,
 *     ),
 *
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="rating", type="integer", example="1"),
 *             @OA\Property(property="message", type="string", example="So sweety"),
 *             @OA\Property(property="commentImages", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="path", type="string", example="tmp/asdjhew"),
 *                     @OA\Property(property="originalName", type="string", example="1.jpg"),
 *                     @OA\Property(property="mimeType", type="string", example="image/jpeg"),
 *                 ),
 *             ),
 *         ),
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="ok",
 *     ),
 * ),
 */

class SwaggerOAController extends Controller
{
}


