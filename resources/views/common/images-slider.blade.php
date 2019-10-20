@if(isset($images) && count($images) > 0)
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <?php $select = 'active'; $index = 0; ?>
        @foreach($images AS $image)
            <li data-target="#carousel-example-generic" data-slide-to="{{ $index++ }}" class="{{ $select }}"></li>
            <?php $select = ''; ?>
        @endforeach
    </ol>
    <div class="carousel-inner">
        <?php $select = 'active'; ?>
        @foreach($images AS $image)
            <div class="item {{ $select }}">
                <img class="img-responsive center-block" src="{{ $image->thumbnailFull() }}">
            </div>
            <?php $select = ''; ?>
        @endforeach
    </div>
    <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
        <span class="fa fa-angle-left"></span>
    </a>
    <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
        <span class="fa fa-angle-right"></span>
    </a>
</div>
@else
    <div class="">
        <img class="img-responsive center-block" src="{{ asset('img/no-image.png') }}">
    </div>
@endif