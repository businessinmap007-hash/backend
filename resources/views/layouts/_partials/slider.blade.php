<!-- start slider -->
<div class="slider">
    <div class="container-fluid">
        <div class="row">
            <div id="myCarousel-1" class="carousel slide" data-ride="carousel">

                <!-- Wrapper for slides -->
                <div class="carousel-inner">

                    @foreach($sliders as $key => $slider)
                        <div class="item {{ $key == 0  ? "active" : ""}}">
                            <img src="{{ asset('public/'.$slider->image) }}" alt="">
                        </div>
                    @endforeach


                </div>

                <!-- Left and right controls -->
                <a class="left carousel-control" href="#myCarousel-1" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel-1" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                    <span class="sr-only">Next</span>
                </a>

            </div>

        </div>
    </div>
</div>
<!-- end slider -->