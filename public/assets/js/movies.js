$(document).ready(function () {
  fetchPopularMovies();

  $("#search-button").click(function () {
    const query = $("#search-input").val().trim();
    if (query) {
      searchMovies(query);
    }
  });

  $("#search-input").keypress(function (e) {
    if (e.key === "Enter") {
      const query = $(this).val().trim();
      if (query) {
        searchMovies(query);
      }
    }
  });
});

function fetchPopularMovies() {
  console.log("Fetching popular movies...");

  $.ajax({
    url: "/api/movies",
    method: "GET",
    dataType: "json",
    success: function (data) {
      console.log("API Response data:", data);

      if (data.results && data.results.length > 0) {
        console.log(`Displaying ${data.results.length} movies`);
        displayMovies(data.results);
      } else {
        console.error("No movie found in data:", data);
        displayError("No movies found. Please try again later.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error fetching popular movies:", error);
      displayError(
        "Could not load movies. Please try again later. Error: " + error
      );
    },
  });
}

function searchMovies(query) {
  $.ajax({
    url: "/api/movies",
    method: "GET",
    data: { query: query },
    dataType: "json",
    success: function (data) {
      console.log("Search API Response data:", data);

      const moviesGrid = $("#movies-grid");
      moviesGrid.empty();

      if (data.results && data.results.length > 0) {
        console.log(`Displaying ${data.results.length} search results`);
        displayMovies(data.results);
      } else {
        moviesGrid.html("<p>No movies found matching your search.</p>");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error searching movies:", error);
      displayError("Search failed. Please try again later. Error: " + error);
    },
  });
}

function displayError(message) {
  const moviesGrid = $("#movies-grid");
  moviesGrid.html(`<p class="error-message">${message}</p>`);
}

function displayMovies(movies) {
  const moviesGrid = $("#movies-grid");
  moviesGrid.empty();

  $.each(movies, function (index, movie) {
    const posterPath = movie.poster_path
      ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
      : "https://via.placeholder.com/500x750?text=No+Image";

    const releaseYear = movie.release_date
      ? movie.release_date.split("-")[0]
      : "N/A";

    const movieCard = $("<div>").addClass("movie-card");
    movieCard.html(`
            <a href="/movie?id=${movie.id}">
                <img src="${posterPath}" alt="${movie.title}" />
                <div class="movie-info">
                    <h3 class="movie-title">${movie.title}</h3>
                    <p class="movie-year">${releaseYear}</p>
                </div>
            </a>
        `);

    moviesGrid.append(movieCard);
  });
}
