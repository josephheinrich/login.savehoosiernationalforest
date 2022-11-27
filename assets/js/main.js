function deletePost(div) {
    if (confirm("Are you sure you want to delete this post?")) {
    console.log("clicked");
    var postID = this.id;
    console.log(div.id);
    $.ajax({

        url : '../../archive/delete.php?postID=' + div.id,
        type : 'POST',
        success : function (result) {
           console.log (result); // Here, you need to use response by PHP file.
           location.reload();
        },
        error : function () {
           console.log ('error');
        }
   
      });
    }
}


function showLoader() {
   $("#loader").css({"border": "16px solid #f3f3f3", "margin-top": "2rem", "border-top": "16px solid #3498db", "border-radius": "50%", "width": "120px", "height": "120px", "animation": "spin 2s linear infinite"});
}

function hideLoader() {
   $("#loader").removeAttr("style");
}