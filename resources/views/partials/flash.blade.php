@if(session('ok'))<div class="flash ok">{{ session('ok') }}</div>@endif
@if(session('convite'))
  <div class="card"><b>Link de convite</b> <span class="mut">(envie por WhatsApp ou e-mail; só aparece agora)</span>
    <div class="link" id="convite">{{ session('convite') }}</div>
    <button type="button" class="btn sec sm" style="margin-top:8px" onclick="navigator.clipboard.writeText(document.getElementById('convite').innerText);this.innerText='Copiado!'">Copiar link</button>
  </div>
@endif
@if($errors->any())<div class="flash err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
