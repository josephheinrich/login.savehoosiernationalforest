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