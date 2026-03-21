{{--
  SetSessionCookieSlot ミドルウェアと整合させるため、
  ?slot= または Referer 経由で解決した slot でも POST 時に同じセッションCookie名を使う（419 防止）。
--}}
@php
    $slotForForm = request()->filled('slot')
        ? request('slot')
        : request()->attributes->get('resolved_slot');
@endphp
@if (is_string($slotForForm) && $slotForForm !== '')
    <input type="hidden" name="slot" value="{{ $slotForForm }}">
@endif
