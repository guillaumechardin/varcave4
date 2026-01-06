$(document).ready(function() {

  /**
   * hide annoucement content and change chevron-type
   */
  $(document).on('click', '.card-header-icon', function() {
    
      const $article = $(this).closest('article');
      Logger.debug('click');
      const $articleBody = $article.find('.message-body');
      const $articleButton = $article.find('.card-header-icon span i');
     
      $articleBody.toggle();
      $articleButton.toggleClass('bi-chevron-up bi-chevron-down');


  });  

});