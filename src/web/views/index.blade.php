@extends('stemmer::base')

@section('css')
    <style>
        #title{
            margin: 20px;
        }
        textarea{
            margin-bottom: 20px;
            resize: none;

            width: 600px;
            height: 120px;
            border: 3px solid #ccc;
            padding: 5px;
            font-family: Tahoma, sans-serif;
            background-position: bottom right;
            background-repeat: no-repeat;
        }
        input[type='submit']{
            padding: 10px;
            background-color: #ccc;
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }

        .btn {
            color: #333!important;
            width: 180px;
            height: 60px;
            cursor: pointer;
            background: transparent;
            border: 1px solid #91C9FF;
            outline: none;
            transition: 1s ease-in-out;
        }

        svg {
            position: absolute;
            left: 0;
            top: 0;
            fill: none;
            stroke: #fff;
            stroke-dasharray: 150 480;
            stroke-dashoffset: 150;
            transition: 1s ease-in-out;
        }

        .btn:hover {
            transition: 1s ease-in-out;
            background: #4F95DA;
        }

        .btn:hover svg {
            stroke-dashoffset: -480;
        }

        .btn span {
            color: white;
            font-size: 18px;
            font-weight: 100;
        }
    </style>
@endsection

@section('body')
    <h2 id="title">{{ __('stemmer::stemmer.header') }}</h2>
    <form action="{{ route('stem') }}" method="POST">
        @csrf
        <textarea placeholder="{{ __('stemmer::stemmer.textarea') }}" name="string" autocomplete="off" rows="10" cols="60"></textarea> <br />
        <input class="btn" type="submit" value="{{ __('stemmer::stemmer.submit') }}">
    </form>
@endsection
