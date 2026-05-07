<!DOCTYPE html>
<html lang="en">
<head>
    </head>
<body>
    <div class="wrapper">
        @include('partials.sidebar')

        <div class="main-panel">
            <div class="main-header">
                @include('partials.navbar')
            </div>

            <div class="container">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>

            @include('partials.footer')
        </div>
    </div>

    @stack('scripts') </body>
</html>
