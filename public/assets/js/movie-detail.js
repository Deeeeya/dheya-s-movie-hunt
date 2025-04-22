document.addEventListener("DOMContentLoaded", () => {
  // Get movie ID from URL
  const urlParams = new URLSearchParams(window.location.search);
  const movieId = urlParams.get("id");

  if (movieId) {
    // Fetch movie details
    fetchMovieDetails(movieId);

    // Fetch reviews for this movie
    fetchReviews(movieId);

    // Set up review form submission
    const reviewForm = document.getElementById("review-form");
    reviewForm.addEventListener("submit", (e) => {
      e.preventDefault();
      submitReview(movieId);
    });
  } else {
    // Redirect to movies page if no ID is provided
    window.location.href = "/movies";
  }
});

// Fetch movie details from our backend API which handles the TMDB API call
async function fetchMovieDetails(movieId) {
  try {
    const response = await fetch(`/api/movie?id=${movieId}`);
    const movie = await response.json();

    if (movie.id) {
      displayMovieDetails(movie);
    } else {
      displayError("Could not load movie details. Please try again later.");
    }
  } catch (error) {
    console.error("Error fetching movie details:", error);
    displayError("Could not load movie details. Please try again later.");
  }
}

// Display error message
function displayError(message) {
  const movieDetailsSection = document.getElementById("movie-details");
  movieDetailsSection.innerHTML = `<p class="error-message">${message}</p>`;
}

// Display movie details
function displayMovieDetails(movie) {
  const movieDetailsSection = document.getElementById("movie-details");

  const posterPath = movie.poster_path
    ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
    : "https://via.placeholder.com/500x750?text=No+Image";

  const releaseYear = movie.release_date
    ? movie.release_date.split("-")[0]
    : "N/A";

  // Update page title
  document.title = `${movie.title} - Movie Watchlist`;

  movieDetailsSection.innerHTML = `
        <img src="${posterPath}" alt="${movie.title}" class="movie-poster">
        <div class="movie-details-info">
            <h1 class="movie-details-title">${movie.title}</h1>
            <p class="movie-details-meta">
                <span>${releaseYear}</span> | 
                <span>${
                  movie.runtime ? `${movie.runtime} min` : "N/A"
                }</span> | 
                <span>${
                  movie.genres
                    ? movie.genres.map((genre) => genre.name).join(", ")
                    : "N/A"
                }</span>
            </p>
            <p class="movie-overview">${movie.overview}</p>
        </div>
    `;
}

// Fetch reviews for a movie from our backend
async function fetchReviews(movieId) {
  try {
    const response = await fetch(`/api/reviews?movie_id=${movieId}`);
    const result = await response.json();

    const reviewsContainer = document.getElementById("reviews-container");

    if (result.status === "success" && result.data && result.data.length > 0) {
      displayReviews(result.data);
    } else {
      reviewsContainer.innerHTML =
        "<p>No reviews yet. Be the first to review!</p>";
    }
  } catch (error) {
    console.error("Error fetching reviews:", error);
    const reviewsContainer = document.getElementById("reviews-container");
    reviewsContainer.innerHTML =
      "<p>Could not load reviews. Please try again later.</p>";
  }
}

// Display reviews
function displayReviews(reviews) {
  const reviewsContainer = document.getElementById("reviews-container");
  reviewsContainer.innerHTML = "";

  reviews.forEach((review) => {
    const stars = "⭐".repeat(review.rating);
    const reviewElement = document.createElement("div");
    reviewElement.className = "review";
    reviewElement.innerHTML = `
            <div class="review-header">
                <span class="review-username">${review.username}</span>
                <span class="review-rating">${stars}</span>
            </div>
            <p class="review-comment">${review.comment}</p>
            <p class="review-date">${new Date(
              review.created_at
            ).toLocaleDateString()}</p>
        `;

    reviewsContainer.appendChild(reviewElement);
  });
}

// Submit a new review
async function submitReview(movieId) {
  const username = document.getElementById("username").value.trim();
  const rating = document.getElementById("rating").value;
  const comment = document.getElementById("comment").value.trim();
  const formErrors = document.getElementById("form-errors");

  // Clear previous error messages
  formErrors.textContent = "";

  // Client-side validation
  if (!username || !comment) {
    formErrors.textContent = "Please fill out all fields";
    return;
  }

  // Prepare review data
  const reviewData = {
    movie_id: movieId,
    username: username,
    rating: parseInt(rating),
    comment: comment,
  };

  try {
    const response = await fetch("/api/reviews", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(reviewData),
    });

    const result = await response.json();

    if (result.status === "success") {
      // Clear form
      document.getElementById("review-form").reset();

      // Show success message
      formErrors.textContent = "Review added successfully!";
      formErrors.style.color = "green";

      // Refresh reviews
      fetchReviews(movieId);

      // Clear success message after 3 seconds
      setTimeout(() => {
        formErrors.textContent = "";
        formErrors.style.color = "#e74c3c";
      }, 3000);
    } else {
      formErrors.textContent = result.message || "Error submitting review";
    }
  } catch (error) {
    console.error("Error submitting review:", error);
    formErrors.textContent = "Error submitting review. Please try again.";
  }
}
