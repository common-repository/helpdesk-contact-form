(function () {
  // Write your code here to prevent any unwanted global variables from being created.
  
  const refreshButton = document.querySelector('#refresh-button');

  const refreshPage = () => {
    location.reload(true);
  }
  
  refreshButton.addEventListener('click', refreshPage)
})();
