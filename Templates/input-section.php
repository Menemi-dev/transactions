<fieldset class="border border-dark p-2 mt-3 container">
    <legend class="w-auto px-2">Specify files to compare</legend>
    <form id="compareFiles" method="post" enctype="multipart/form-data" onsubmit="return false;">
        <div class="form-row my-2">
            <div class="col-sm-3">
                <span>Select file 1</span>
            </div>
            <div class="col-sm-6">
                <input type="file" id="file1" accept=".csv" class="form-control p-1">
            </div>
        </div>
        <div class="form-row my-2">
            <div class="col-sm-3">
                <span>Select file 2</span>
            </div>
            <div class="col-sm-6">
                <input type="file" id="file2" accept=".csv" class="form-control p-1">
            </div>
        </div>
        <div class="form-row my-2">
            <div class="col-12 mt-4 text-center">
                <input type="submit" value="Compare" onclick="compareFiles(); return false;">
            </div>
        </div>
    </form>
    <div id="inputAlert" class="alert alert-danger d-none" role="alert">Files can't be empty</div>
</fieldset>