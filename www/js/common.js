function uploadFile(idProd){
    var data = new FormData();
    data.append('new_image_file', $('#file_input_id' + idProd).prop('files')[0]);
    data.append('id',idProd);


    $.ajax({
        type: 'POST',
        processData: false,
        contentType: false,
        async: false,
        data: data,
        //url: {link Admin:upload},
        url: './upload',
    dataType : 'json',
        success: function(jsonData){

    }
});

    location.reload();
}

function changeCategory(idProd, idCat) {
    var data = new FormData();
    data.append('idProd',idProd);
    data.append('idCat',idCat);


    $.ajax({
        type: 'POST',
        processData: false,
        contentType: false,
        async: false,
        data: data,
        //url: {link Admin:changecat},
        url: './changecat',
    dataType : 'json',
        success: function(jsonData){

    }
});

    location.reload();

}

function changeSubcategory(idCat, idParCat) {
    var data = new FormData();
    data.append('idCat', idCat);
    data.append('idParCat', idParCat);

    $.ajax({
        type: 'POST',
        processData: false,
        contentType: false,
        async: false,
        data: data,
        url: './changeparcat',
        dataType: 'json',
        error: function (xhr, status, error) {
            alert('Při změně rodičovské kategorie došlo k chybě ! (' + $.parseJSON(xhr.responseText).error + ')');
        }
    });

    location.reload();
}