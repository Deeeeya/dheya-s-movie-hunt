document.addEventListener("DOMContentLoaded", () => {
  // Load popular movies when the page loads
  fetchPopularMovies();

  // Set up search functionality
  const searchButton = document.getElementById("search-button");
  const searchInput = document.getElementById("search-input");

  searchButton.addEventListener("click", () => {
    const query = searchInput.value.trim();
    if (query) {
      searchMovies(query);
    }
  });

  searchInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      const query = searchInput.value.trim();
      if (query) {
        searchMovies(query);
      }
    }
  });
});

// Fetch popular movies from our backend API which handles the TMDB API call
async function fetchPopularMovies() {
  try {
    console.log("Fetching popular movies...");
    const response = await fetch("/api/movies");
    console.log("API Response status:", response.status);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log("API Response data:", data);

    if (data.results && data.results.length > 0) {
      console.log(`Displaying ${data.results.length} movies`);
      displayMovies(data.results);
    } else {
      console.error("No movie results found in data:", data);
      displayError("No movies found. Please try again later.");
    }
  } catch (error) {
    console.error("Error fetching popular movies:", error);
    displayError(
      "Could not load movies. Please try again later. Error: " + error.message
    );
  }
}

// Search for movies using our backend API
async function searchMovies(query) {
  try {
    console.log(`Searching for movies with query: ${query}`);
    const response = await fetch(
      `/api/movies?query=${encodeURIComponent(query)}`
    );
    console.log("Search API Response status:", response.status);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log("Search API Response data:", data);

    const moviesGrid = document.getElementById("movies-grid");
    moviesGrid.innerHTML = "";

    if (data.results && data.results.length > 0) {
      console.log(`Displaying ${data.results.length} search results`);
      displayMovies(data.results);
    } else {
      moviesGrid.innerHTML = "<p>No movies found matching your search.</p>";
    }
  } catch (error) {
    console.error("Error searching movies:", error);
    displayError(
      "Search failed. Please try again later. Error: " + error.message
    );
  }
}

// Display error message
function displayError(message) {
  const moviesGrid = document.getElementById("movies-grid");
  moviesGrid.innerHTML = `<p class="error-message">${message}</p>`;
}

// Display movies in the grid
function displayMovies(movies) {
  const moviesGrid = document.getElementById("movies-grid");
  moviesGrid.innerHTML = "";

  movies.forEach((movie) => {
    const posterPath = movie.poster_path
      ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
      : "https://via.placeholder.com/500x750?text=No+Image";

    const releaseYear = movie.release_date
      ? movie.release_date.split("-")[0]
      : "N/A";

    const movieCard = document.createElement("div");
    movieCard.className = "movie-card";
    movieCard.innerHTML = `
            <a href="/movie?id=${movie.id}">
                <img src="${posterPath}" alt="${movie.title}" />
                <div class="movie-info">
                    <h3 class="movie-title">${movie.title}</h3>
                    <p class="movie-year">${releaseYear}</p>
                </div>
            </a>
        `;

    moviesGrid.appendChild(movieCard);
  });
}

// Fallback movies in case API fails completely
function displayFallbackMovies() {
  const fallbackMovies = [
    {
      id: 550,
      title: "Fight Club",
      release_date: "1999-10-15",
      poster_path: null,
    },
    {
      id: 299536,
      title: "Avengers: Infinity War",
      release_date: "2018-04-25",
      poster_path: null,
    },
    {
      id: 278,
      title: "The Shawshank Redemption",
      release_date: "1994-09-23",
      poster_path: null,
    },
  ];

  displayMovies(fallbackMovies);
}
