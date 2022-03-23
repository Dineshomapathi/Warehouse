<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Item Check In/Out
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <video id="preview" style="width:500px;height: 500px;margin:0px auto;"></video>
            </div>
        </div>
    </div>

    @section('script')

    <script type="text/javascript">
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
        scanner.addListener('scan', function (content) {
            fetch('/checkstatusitem/' + content)
            .then(res => {
                if(res.ok) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'User found and updated',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    return res.json()
                }else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'User not found contact admin',
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            })
            .then(data => console.log(data))
            .catch(function (e) {console.error(e);});
        });
        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
            scanner.start(cameras[0]);
            } else {
            console.error('No cameras found.');
            }
        }).catch(function (e) {
            console.error(e);
        });
    </script>

    @endsection


</x-app-layout>
