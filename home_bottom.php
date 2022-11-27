</div>
    </div>  
    <script type="text/javascript">
    <?php 
    echo $script_tag
    ?>
    </script>
    <script>
        document.getElementById('file_to_upload').addEventListener('change', (event) => {
            window.selectedFile = event.target.files[0];
            document.getElementById('file_name').innerHTML = window.selectedFile.name;
            uploadFile(window.selectedFile);
        });

        function uploadFile(file) {
            var formData = new FormData();
            formData.append('file_to_upload', file);
            var ajax = new XMLHttpRequest();
            ajax.upload.addEventListener("progress", progressHandler, false);
            ajax.open('POST', 'upload.php');
            ajax.send(formData);
        }

        function progressHandler(event) {
            var percent = (event.loaded / event.total) * 100;
            document.getElementById("progress_bar").value = Math.round(percent);
            document.getElementById("progress_status").innerHTML = Math.round(percent) + "% uploaded";
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>