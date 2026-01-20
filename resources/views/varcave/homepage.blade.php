@include('varcave.template.header')
@include('varcave.template.navbar')

<script src="/varcave/homepage.js"></script>
<section class="section">
    <div class="container">
      <h1 class="title is-2">
        {{ __('varcave.homepage.hometext') }}
      </h1>
      <p class="subtitle">
       {{ $settings->get('legal_notice') }}
       <p><a href="http://192.168.1.100:3000/cave/dc10957e-0e00-4b20-acb5-312a391c4c46?display=legacy&id=1111">Affichage classique</a></p>
       <p><a href="http://192.168.1.100:3000/cave/dc10957e-0e00-4b20-acb5-312a391c4c46?display=v4">Affichage Varcave4</a></p>
      </p>
      <p>
        @guest
        {{ __('varcave.homepage.connectinfo') }}
        @endguest
      </p>
    </div>
</section>

<section class="section">
  <div class="container">
    <div class="columns is-5">
      <div class="column ml-2 mr-2">
          <h3 class="title is-3">{{ __('varcave.homepage.homeAnnouncements') }}</h3>
          @foreach($homeAnnouncements as $announce)
            <!-- <div class="card">
              <header class="card-header">
                <p class="card-header-title">{{$announce->title}}</p>
              </header>
              <div class="card-content">
                <div class="content">
                  {!! $announce->content !!}
                </div>
              </div>
            </div>-->

            <article id="announcement-{{$announce->id}}" class="message">
              <div class="message-header">
                <p>{{$announce->title}}</p>
                <button class="card-header-icon" aria-label="more options">
                  <span class="icon">
                    <i class="bi bi-chevron-up"></i>
                  </span>
                </button>
              </div>
              <div class="message-body">
                {!! $announce->content !!}
              </div>
            </article>

          @endforeach
      </div>

      <div class="column">
        <h3 class="title is-3">{{ __('varcave.homepage.lastCavesUpdates')}}</h2>
        @foreach($caveChangelogs as $cavelog)
            <div class="card is-link">
              <div class="card-content">
                <div class="media">
                  <div class="media-left">
                    <figure class="image is-48x48">
                      <img
                        src="https://bulma.io/assets/images/placeholders/96x96.png"
                        alt="Placeholder image"
                      />
                    </figure>
                  </div>
                  <a href="{{url('cave/' . $cavelog->cave->uuid)}}"><div class="media-content ">
                    <p class="title is-5 has-text-weight-semibold">{{ $cavelog->cave->name }}</p>
                    <p class="subtitle is-7">{{$cavelog->created_at}}</p>
                  </div></a>
                </div>

                <div class="content">
                  {{$cavelog->modification_note}}
                </div>
              </div>
            </div>

          @endforeach
      </div>
      <div class="column">
        <h3 class="title is-3">{{ __('varcave.homepage.randomCave')}}</h3>
        <div class="card is-link">
          <div class="card-content">
            <div class="media">
              <div class="media-left">
                <figure class="image is-48x48">
                  <img
                    src="https://bulma.io/assets/images/placeholders/96x96.png"
                    alt="Placeholder image"
                  />
                </figure>
              </div>
              <a href="{{ url('cave/' . $randomCave->uuid) }}"><div class="media-content ">
                <p class="title is-5 has-text-weight-semibold">{{ $randomCave->name }}</p>
                <p class="subtitle is-7">{{$randomCave->created_at}}</p>
              </div></a>
            </div>

            <div class="content">
              {{ \Illuminate\Support\Str::limit($randomCave->description, 160)}}
              <div class="grid">
                @foreach($caveFiles as $files)
                <div class="cell">
                  <img src="{{ Storage::url($files->file_path) }}"></img>
                </div>  
                @endforeach
                
              </div>
            </div>
          </div>
        </div>
        
      </div>   
    </div>
  </div>
</section>

@include('varcave.template.footer')
