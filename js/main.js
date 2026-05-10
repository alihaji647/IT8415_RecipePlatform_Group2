$(document).ready(function() {
    console.log('Recipe Platform JS Loaded! 🚀');
    
    // Comment form submission
    $(document).on('submit', '#commentForm', function(e) {
        e.preventDefault();
        console.log('Submitting comment...');
        
        $.ajax({
            url: 'ajax/add_comment.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                if(response.success) {
                    // Clear form
                    $('#commentForm')[0].reset();
                    // Reload comments
                    $('#comments-container').load('ajax/load_comments.php?recipe_id=' + $('#recipe_id').val());
                    // Success message
                    $('#comment-message').html('<div class="alert alert-success">Comment added! ✅</div>').fadeOut(3000);
                } else {
                    alert('Error: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('AJAX error. Please refresh and try again.');
            }
        });
    });
});