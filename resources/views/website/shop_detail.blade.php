@extends('website.layout')

@section('title', 'Home')


@section('content')

    <body>       

        <!-- Single Page Header start -->
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Shop</h1>
        </div>
        <!-- Single Page Header End -->


        <!-- Single Product Start -->
        <div class="container-fluid py-5 mt-5">
            <div class="container py-5">
                <div class="row g-4 mb-5">
                    <div class="col-lg-8 col-xl-9">
                        <div class="row g-4">

                            <div class="col-lg-6">
                                <div class="border rounded">
                                    <a href="#">
                                        <img src="{{ asset('storage/products/'.$product->product_images[0]) }}"
                                        class="img-fluid rounded">
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h4 class="fw-bold mb-3">{{ $product->name }}</h4>
                                <p class="mb-3">Category: {{ $product->category->name ?? 'N/A' }}</p>
                                <p class="fs-4 fw-bold text-primary">₹{{ $product->mrp }}</p>
                                
                                <form action="{{ route('add_cart') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="input-group quantity mb-3" style="width: 120px;">
                                        <button type="button" class="btn btn-sm btn-minus">-</button>

                                        <input type="number" name="qty" value="1" min="1"
                                        class="form-control text-center">

                                        <button type="button" class="btn btn-sm btn-plus">+</button>
                                    </div>

                                    <button class="btn border border-secondary rounded-pill px-3 text-primary">
                                        <i class="fa fa-shopping-bag me-2"></i> Add to cart
                                    </button>
                                </form>

                            </div>

                            <div class="col-lg-12">

                                <nav>
                                    <div class="nav nav-tabs mb-3">
                                        <button class="nav-link active border-white border-bottom-0" type="button" role="tab"
                                            id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                            aria-controls="nav-about" aria-selected="true">Description</button>
                                        <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                            id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                            aria-controls="nav-mission" aria-selected="false">Reviews</button>
                                    </div>
                                </nav>

                                <div class="tab-content mb-5">

                                    <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                    
                                        <p class="mb-4">{{ $product->description }}</p>
                                   
                                        <!-- <div class="px-2">
                                            <div class="row g-4">
                                                <div class="col-6">
                                                    <div class="row bg-light align-items-center text-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">Weight</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">1 kg</p>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center align-items-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">Country of Origin</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">Agro Farm</p>
                                                        </div>
                                                    </div>
                                                    <div class="row bg-light text-center align-items-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">Quality</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">Organic</p>
                                                        </div>
                                                    </div>
                                                    <div class="row text-center align-items-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">Сheck</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">Healthy</p>
                                                        </div>
                                                    </div>
                                                    <div class="row bg-light text-center align-items-center justify-content-center py-2">
                                                        <div class="col-6">
                                                            <p class="mb-0">Min Weight</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0">250 Kg</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->

                                    </div>

                                    <div class="tab-pane" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                                        <!-- <div class="d-flex">
                                            <img src="img/avatar.jpg" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                            <div class="">
                                                <p class="mb-2" style="font-size: 14px;">April 12, 2024</p>
                                                <div class="d-flex justify-content-between">
                                                    <h5>Jason Smith</h5>
                                                    <div class="d-flex mb-3">
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <p>The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic 
                                                    words etc. Susp endisse ultricies nisi vel quam suscipit </p>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <img src="img/avatar.jpg" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                            <div class="">
                                                <p class="mb-2" style="font-size: 14px;">April 12, 2024</p>
                                                <div class="d-flex justify-content-between">
                                                    <h5>Sam Peters</h5>
                                                    <div class="d-flex mb-3">
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star text-secondary"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <p class="text-dark">The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic 
                                                    words etc. Susp endisse ultricies nisi vel quam suscipit </p>
                                            </div>
                                        </div> -->
                                    </div>

                                    <div class="tab-pane" id="nav-vision" role="tabpanel">
                                        <p class="text-dark">Tempor erat elitr rebum at clita. Diam dolor diam ipsum et tempor sit. Aliqu diam
                                            amet diam et eos labore. 3</p>
                                        <p class="mb-0">Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam et eos labore.
                                            Clita erat ipsum et lorem et sit</p>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-lg-4 col-xl-3">
                        <div class="row g-4 fruite">
                            
                            <div class="col-lg-12">
                                <div class="position-relative">
                                    <img src="/website/img/banner-fruits.jpg" class="img-fluid w-100 rounded" alt="">
                                    <div class="position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%);">
                                        <h3 class="text-secondary fw-bold">Fresh <br> Fruits <br> Banner</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h1 class="fw-bold mb-0">Related products</h1>

                <div class="vesitable">
                    <div class="owl-carousel vegetable-carousel justify-content-center">

                        @foreach($relatedProducts as $related)
                            <div class="border border-primary rounded position-relative vesitable-item">

                                <div class="vesitable-img">
                                    <img src="{{ asset('storage/products/'.$related->product_images[0]) }}"
                                        class="img-fluid rounded">
                                </div>

                                <div class="text-white bg-primary px-3 py-1 rounded position-absolute"
                                    style="top: 10px; right: 10px;">
                                    {{ $related->category->name ?? 'Category' }}
                                </div>

                                <div class="p-4 pb-0 rounded-bottom">
                                    <h4>{{ $related->name }}</h4>

                                    <div class="d-flex justify-content-between flex-lg-wrap">
                                        <p class="fs-4 fw-bold text-primary">₹{{ $related->mrp }}</p>

                                        <form action="{{ route('add_cart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $related->id }}">

                                            <button class="btn border border-secondary rounded-pill px-3 text-primary">
                                                <i class="fa fa-shopping-bag me-2"></i> Add to cart
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                    </div>
                </div>

            </div>
        </div>
        <!-- Single Product End -->
    
