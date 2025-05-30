$(document).ready(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const movieId = urlParams.get("id");

  if (movieId) {
    fetchMovieDetails(movieId);
    fetchReviews(movieId);

    $("#review-form").submit(function (e) {
      e.preventDefault();
      submitReview(movieId);
    });
  } else {
    window.location.href = "/movies";
  }
});

function fetchMovieDetails(movieId) {
  $.ajax({
    url: "/api/movie",
    method: "GET",
    data: { id: movieId },
    dataType: "json",
    success: function (movie) {
      if (movie.id) {
        displayMovieDetails(movie);
      } else {
        displayError("Could not load movie details. Please try again later.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error fetching movie details:", error);
      displayError("Could not load movie details. Please try again later.");
    },
  });
}

function displayError(message) {
  $("#movie-details").html(`<p class="error-message">${message}</p>`);
}

function displayMovieDetails(movie) {
  const movieDetailsSection = $("#movie-details");

  const posterPath = movie.poster_path
    ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
    : "https://via.placeholder.com/500x750?text=No+Image";

  const releaseYear = movie.release_date
    ? movie.release_date.split("-")[0]
    : "N/A";

  document.title = `${movie.title} - Movie Watchlist`;

  movieDetailsSection.html(`
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
    `);
}

function fetchReviews(movieId) {
  $.ajax({
    url: "/api/reviews",
    method: "GET",
    data: { movie_id: movieId },
    dataType: "json",
    success: function (result) {
      const reviewsContainer = $("#reviews-container");

      if (
        result.status === "success" &&
        result.data &&
        result.data.length > 0
      ) {
        displayReviews(result.data);
      } else {
        reviewsContainer.html("<p>No reviews yet. Be the first to review!</p>");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error fetching reviews:", error);
      $("#reviews-container").html(
        "<p>Could not load reviews. Please try again later.</p>"
      );
    },
  });
}

function displayReviews(reviews) {
  const reviewsContainer = $("#reviews-container");
  reviewsContainer.empty();

  $.each(reviews, function (index, review) {
    const stars = "⭐".repeat(review.rating);
    const reviewElement = $("<div>").addClass("review");
    reviewElement.html(`
            <div class="review-header">
                <span class="review-username">${review.username}</span>
                <span class="review-rating">${stars}</span>
            </div>
            <p class="review-comment">${review.comment}</p>
            <p class="review-date">${new Date(
              review.created_at
            ).toLocaleDateString()}</p>
        `);

    reviewsContainer.append(reviewElement);
  });
}

function submitReview(movieId) {
  const username = $("#username").val().trim();
  const rating = $("#rating").val();
  const comment = $("#comment").val().trim();
  const formErrors = $("#form-errors");

  formErrors.text("");
  formErrors.css("color", "#e74c3c");

  if (!username || !comment) {
    formErrors.text("Please fill out all fields");
    return;
  }

  console.log("Submitting review:", {
    movie_id: movieId,
    username,
    rating,
    comment,
  });

  const reviewData = {
    movie_id: movieId,
    username: username,
    rating: parseInt(rating),
    comment: comment,
  };

  formErrors.text("Submitting review...");
  formErrors.css("color", "blue");

  $.ajax({
    url: "/api/reviews",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(reviewData),
    dataType: "json",
    success: function (result) {
      console.log("Response data:", result);

      if (result.status === "success") {
        $("#review-form")[0].reset();

        formErrors.text(result.message || "Review added successfully!");
        formErrors.css("color", "green");

        fetchReviews(movieId);

        setTimeout(function () {
          formErrors.text("");
        }, 3000);
      } else {
        formErrors.text(result.message || "Error submitting review");
        formErrors.css("color", "#e74c3c");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error submitting review:", error);
      formErrors.text("Network error submitting review. Please try again.");
      formErrors.css("color", "#e74c3c");
    },
  });
}
