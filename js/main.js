$(document).ready(function() {
    console.log('Recipe Platform JS Loaded! 🚀');
    
    // 🔥 Comment form AJAX submission
    $(document).on('submit', '#commentForm', function(e) {
        e.preventDefault();
        console.log('Submitting comment...');
        
        // Disable submit button during request
        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...');
        
        $.ajax({
            url: 'ajax/add_comment.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                if(response.success) {
                    // Clear form inputs
                    $('#commentForm')[0].reset();
                    // Reload comments dynamically
                    $('#comments-container').load('ajax/load_comments.php?recipe_id=' + $('#recipe_id').val());
                    // Show success message with fade out
                    $('#comment-message').html('<div class="alert alert-success">Comment added! ✅</div>').fadeIn().delay(3000).fadeOut();
                } else {
                    // Show error message
                    alert('Error: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('AJAX error. Please refresh and try again.');
            },
            // Re-enable button on completion
            complete: function() {
                $submitBtn.prop('disabled', false).html('Post Comment');
            }
        });
    });
    
    // 🔥 Delete confirmation with sweet loading
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        if(confirm('Are you sure? This cannot be undone.')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function() {
                    location.reload(); // Refresh page
                },
                error: function() {
                    alert('Delete failed. Please try again.');
                }
            });
        }
    });
});