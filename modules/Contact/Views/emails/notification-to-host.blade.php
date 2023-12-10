@extends('Email::layout')
@section('content')

    <div class="b-container">
        <div class="b-panel">
            <h3 class="email-headline"><strong>{{__('Hello')}}</strong></h3>
            <p>A user has request to contact you, below is his details</p>
            <br>
            <div class="b-panel">
                <div class="b-table-wrap">
                    <table class="b-table" cellspacing="0" cellpadding="0">
                        <tr class="info-first-name">
                            <td class="label">{{__('Name')}}</td>
                            <td class="val">{{$name}}</td>
                        </tr>
                        <tr class="info-first-name">
                            <td class="label">{{__('Email')}}</td>
                            <td class="val">{{$email}}</td>
                        </tr>
                        <tr class="info-first-name">
                            <td class="label">{{__('Phone')}}</td>
                            <td class="val">{{$phone}}</td>
                        </tr>
                        <tr class="info-first-name">
                            <td class="label">{{__('Message')}}</td>
                            <td class="val">{{$messageText}}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
