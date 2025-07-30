// Add gradient backgrounds for posts without featured images
function addGradientToPostsWithoutImages() {
  // Wait for DOM to be ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', addGradientToPostsWithoutImages);
    return;
  }

  // Find all post blocks
  const posts = document.querySelectorAll('li.wp-block-post, .wp-block-post');
  
  posts.forEach(post => {
    // Check if this post has a featured image
    const hasFeaturedImage = post.querySelector('.wp-block-post-featured-image, .wp-block-image, img');
    
    if (!hasFeaturedImage) {
      // Add class to enable gradient background
      post.classList.add('no-featured-image');
    } else {
      // Remove class if image exists (for dynamic content)
      post.classList.remove('no-featured-image');
    }
  });
}

// Run initially
addGradientToPostsWithoutImages();

// Also run when new content is loaded (for AJAX/dynamic loading)
const observer = new MutationObserver(() => {
  addGradientToPostsWithoutImages();
});

// Observe changes to the document body
observer.observe(document.body, {
  childList: true,
  subtree: true
});