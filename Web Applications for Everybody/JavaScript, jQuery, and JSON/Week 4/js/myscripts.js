function doValidate()
{
  console.log('Validating...');
  try
  {
    em = document.getElementById('nam').value;
    pw = document.getElementById('id_1723').value;
    console.log("Validating addr="+em+" pw="+pw);
    if ((pw == null || pw == "") || (em == null || em == ""))
    {
      alert("Both fields must be filled out");
      return false;
    }
    return true;
  }
  catch(e)
  {
    return false;
  }
  return false;
}
