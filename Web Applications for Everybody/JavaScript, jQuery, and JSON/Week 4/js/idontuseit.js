$(document).ready(function(){
  window.console && console.log('Document ready called');
  countPos = 1;
  myEle = document.getElementById('position'+countPos);
  if(myEle){
    while(myEle){
      countPos++;
      myEle = document.getElementById('position'+countPos);
    }
    countPos--;
  }
  else {
    countPos = 0;
  }

  $('#addPos').click(function(event){
    event.preventDefault();
    if(countPos >= 9){
      alert("Maximum of nine position entries exceeded");
      return;
    }
    countPos++;
    window.console && console.log("Adding position "+countPos);
    $('#position_fields').append(' \
      <div id="position'+countPos+'"> \
      <p>Year: \
      <input type="text" size="10" name="year'+countPos+'" value="" /> \
      <input type="button" style="height:27px; width:27px" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
      <p><textarea name="desc'+countPos+'" rows="8" cols="80"></textarea></p> \
      </div>');
  });
}); //ending bracket
