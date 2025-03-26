@extends('layouts.master')
@section('title', 'Crawler Movie')
@section('content')
<style>
    label{
        color: #fff;
    }
    .movie-list {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 10px;
        justify-content: flex-start;
    }

    .movie-list li {
        flex: 0 0 calc(20% - 8px);
        box-sizing: border-box;    
        padding: 10px;
        text-align: center;
    }
</style>

<main class="main">
<div class="container-fluid">
    <div class="row">
        <!-- main title -->
        <div class="col-12">
            <div class="main__title">
                <h2>Crawler movies</h2>
            </div>
        </div>
        <!-- end main title -->
        <!-- form -->
        <div class="col-12">
            <form class="form-horizontal" id="admin-{{ $params['prefix'] }}-form" name="admin-{{ $params['prefix'] }}-form"
            enctype="multipart/form-data" method="POST" action="{{ route($params['prefix'] . '.' . $params['controller'] . '.crawler-data') }}">
                <input type="hidden" name="_method" value="POST">
                <div class="row">
                    <div class="col-12 col-md-7 form__content">
                        <div class="row">
                            <div class="col-12">
                                <div class="form__group">
                                    <input id="text" name="url" class="form__input" placeholder="Link" value="https://ophim1.com/danh-sach/phim-moi-cap-nhat"/>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form__group">
                                    <input type="text" class="form__input" id="page_from" name="page_from" placeholder="From page (1)" />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form__group">
                                    <input type="text" class="form__input" id="page_to" name="page_to" placeholder="To page (10)" />
                                </div>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="form__btn">Get Movies</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </form>
            <form id="admin-crawler" action="">
                <div class="col-12 col-md-7 form__content">
                    <ul class="row form__radio show-movie movie-list">
                        <li>
                            <span>List movies:</span>
                        </li>
                    </ul>
                </div>
                <div class="col-12">
                    <button type="submit" class="form__btn">SAVE</button>
                </div>
            </form>
        </div>
        <!-- end form -->
    </div>
</div>
</main>
<script>
    $(document).ready(function() {
        $('#admin-{{ $params['prefix'] }}-form').submit(function(e) {
            
            // showLoadding();
            // $('.input-error').html('');
            // $('.form-group row p-0 m-0 mb-2 input').removeClass('is-invalid');
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route($params['prefix'] . '.' . $params['controller'] . '.crawler-data') }}",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                   console.log(data.data);
                   
                    $('.show-movie').empty().append(`
                        <li><span>List movies:</span></li>
                    `);
                    $.each(data.data, function(index, movie) {
                        if (movie.existed != 0) {
                            $('.show-movie').append(`
                            <li>
                                <input id="movie_${index}" type="checkbox" name="movie_id" value="${movie.id}" disabled/>
                                <label for="movie_${index}">${movie.name} (Đã có)</label>
                            </li>
                        `);
                        }
                        else{
                            $('.show-movie').append(`
                            <li>
                                <input id="movie_${index}" type="checkbox" name="movie_id" value="${movie.id}" checked/>
                                <label for="movie_${index}">${movie.name}</label>
                            </li>
                        `);
                        }
                        
                    });
                },
                error: function(data) {
                    // hideLoadding();

                    // for (x in data.responseJSON.errors) {
                    //     $('#' + x).parents('.form-group').find('.input-error').html(data
                    //         .responseJSON.errors[x]);
                    //     $('#' + x).parents('.form-group').find('.input-error').show();
                    //     $('#' + x).addClass('is-invalid');
                    // }
                }
            });
        });
        $('#admin-crawler').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route($params['prefix'] . '.' . $params['controller'] . '.store') }}",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                   
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });
    });
</script>
@stop
