<!-- Started at 11:57 6-22-2022 -->

<?php
include("includes/includedFiles.php");

if(isset($_GET['id'])) {
	$artistId = $_GET['id'];
}
else {
	header("Location: index.php");
}

$artist = new Artist($con, $artistId);
?>

<div class="entityInfo borderBottom">

	<div class="centerSection">

		<div class="artistInfo">

			<h1 class="artistName"><?php echo $artist->getName(); ?></h1>

			<div class="headerButtons">
				<button class="button green" onclick="playFirstSong()">PLAY</button>
			</div>

		</div>

	</div>

</div>


<div class="tracklistContainer borderBottom">
	<h2>SONGS</h2>
	<ul class="tracklist">
		
		<?php
		$songIdArray = $artist->getSongIds();

		$i = 1;
		foreach($songIdArray as $songId) {

			if($i > 5) {
				break;
			}

			$albumSong = new Song($con, $songId);
			$albumArtist = $albumSong->getArtist();

			echo "<li class='tracklistRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<span class='trackNumber'>$i</span>
					</div>


					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>

					<div class='trackOptions'>
						<img class='optionsButton' src='assets/images/icons/more.png'>
					</div>

					<div class='trackDuration'>
						<span class='duration'>" . $albumSong->getDuration() . "</span>
					</div>


				</li>";

			$i = $i + 1;
		}

		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>

	</ul>
</div>

<div class="gridViewContainer">
	<h2>ALBUMS</h2>
	<?php
		$albumQuery = mysqli_query($con, "SELECT * FROM albums WHERE artist='$artistId'");

		while($row = mysqli_fetch_array($albumQuery)) {
			



			echo "<div class='gridViewItem'>
					<span role='link' tabindex='0' onclick='openPage(\"album.php?id=" . $row['id'] . "\")'>
						<img src='" . $row['artworkPath'] . "'>

						<div class='gridViewInfo'>"
							. $row['title'] .
						"</div>
					</span>

				</div>";



		}
	?>

</div>

<?php
include("includes/includedFiles.php");

if(isset($_GET['term'])) {
	$term = urldecode($_GET['term']);
}
else {
	$term = "";
}
?>

<div class="searchContainer">

	<h4>Search for an artist, album or song</h4>
	<input type="text" class="searchInput" value="<?php echo $term; ?>" placeholder="Start typing..." onfocus="this.value = this.value">

</div>

<script>

$(".searchInput").focus();

$(function() {
	
	$(".searchInput").keyup(function() {
		clearTimeout(timer);

		timer = setTimeout(function() {
			var val = $(".searchInput").val();
			openPage("search.php?term=" + val);
		}, 2000);

	})


})

</script>

<?php if($term == "") exit(); ?>

<div class="tracklistContainer borderBottom">
	<h2>SONGS</h2>
	<ul class="tracklist">
		
		<?php
		$songsQuery = mysqli_query($con, "SELECT id FROM songs WHERE title LIKE '$term%' LIMIT 10");

		if(mysqli_num_rows($songsQuery) == 0) {
			echo "<span class='noResults'>No songs found matching " . $term . "</span>";
		}



		$songIdArray = array();

		$i = 1;
		while($row = mysqli_fetch_array($songsQuery)) {

			if($i > 15) {
				break;
			}

			array_push($songIdArray, $row['id']);

			$albumSong = new Song($con, $row['id']);
			$albumArtist = $albumSong->getArtist();

			echo "<li class='tracklistRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<span class='trackNumber'>$i</span>
					</div>


					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>

					<div class='trackOptions'>
						<img class='optionsButton' src='assets/images/icons/more.png'>
					</div>

					<div class='trackDuration'>
						<span class='duration'>" . $albumSong->getDuration() . "</span>
					</div>


				</li>";

			$i = $i + 1;
		}

		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>

	</ul>
</div>


<div class="artistsContainer borderBottom">

	<h2>ARTISTS</h2>

	<?php
	$artistsQuery = mysqli_query($con, "SELECT id FROM artists WHERE name LIKE '$term%' LIMIT 10");
	
	if(mysqli_num_rows($artistsQuery) == 0) {
		echo "<span class='noResults'>No artists found matching " . $term . "</span>";
	}

	while($row = mysqli_fetch_array($artistsQuery)) {

		$artistFound = new Artist($con, $row['id']);

		echo "<div class='searchResultRow'>
				<div class='artistName'>

					<span role='link' tabindex='0' onclick='openPage(\"artist.php?id=" . $artistFound->getId() ."\")'>
					"
					. $artistFound->getName() .
					"
					</span>

				</div>

			</div>";

	}


	?>

</div>

<div class="gridViewContainer">
	<h2>ALBUMS</h2>
	<?php
		$albumQuery = mysqli_query($con, "SELECT * FROM albums WHERE title LIKE '$term%' LIMIT 10");

		if(mysqli_num_rows($albumQuery) == 0) {
			echo "<span class='noResults'>No albums found matching " . $term . "</span>";
		}

		while($row = mysqli_fetch_array($albumQuery)) {

			echo "<div class='gridViewItem'>
					<span role='link' tabindex='0' onclick='openPage(\"album.php?id=" . $row['id'] . "\")'>
						<img src='" . $row['artworkPath'] . "'>

						<div class='gridViewInfo'>"
							. $row['title'] .
						"</div>
					</span>

				</div>";



		}
	?>

</div>

<?php
	include("includes/config.php");
	include("includes/classes/Account.php");
	include("includes/classes/Constants.php");

	$account = new Account($con);

	include("includes/handlers/register-handler.php");
	include("includes/handlers/login-handler.php");

	function getInputValue($name) {
		if(isset($_POST[$name])) {
			echo $_POST[$name];
		}
	}
?>

<html>
<head>
	<title>Welcome to Slotify!</title>

	<link rel="stylesheet" type="text/css" href="assets/css/register.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
</head>
<body>
	<?php

	if(isset($_POST['registerButton'])) {
		echo '<script>
				$(document).ready(function() {
					$("#loginForm").hide();
					$("#registerForm").show();
				});
			</script>';
	}
	else {
		echo '<script>
				$(document).ready(function() {
					$("#loginForm").show();
					$("#registerForm").hide();
				});
			</script>';
	}

	?>
	

	<div id="background">

		<div id="loginContainer">

			<div id="inputContainer">
				<form id="loginForm" action="register.php" method="POST">
					<h2>Login to your account</h2>
					<p>
						<?php echo $account->getError(Constants::$loginFailed); ?>
						<label for="loginUsername">Username</label>
						<input id="loginUsername" name="loginUsername" type="text" placeholder="e.g. bartSimpson" value="<?php getInputValue('loginUsername') ?>" required>
					</p>
					<p>
						<label for="loginPassword">Password</label>
						<input id="loginPassword" name="loginPassword" type="password" placeholder="Your password" required>
					</p>

					<button type="submit" name="loginButton">LOG IN</button>

					<div class="hasAccountText">
						<span id="hideLogin">Don't have an account yet? Signup here.</span>
					</div>
					
				</form>



				<form id="registerForm" action="register.php" method="POST">
					<h2>Create your free account</h2>
					<p>
						<?php echo $account->getError(Constants::$usernameCharacters); ?>
						<?php echo $account->getError(Constants::$usernameTaken); ?>
						<label for="username">Username</label>
						<input id="username" name="username" type="text" placeholder="e.g. bartSimpson" value="<?php getInputValue('username') ?>" required>
					</p>

					<p>
						<?php echo $account->getError(Constants::$firstNameCharacters); ?>
						<label for="firstName">First name</label>
						<input id="firstName" name="firstName" type="text" placeholder="e.g. Bart" value="<?php getInputValue('firstName') ?>" required>
					</p>

					<p>
						<?php echo $account->getError(Constants::$lastNameCharacters); ?>
						<label for="lastName">Last name</label>
						<input id="lastName" name="lastName" type="text" placeholder="e.g. Simpson" value="<?php getInputValue('lastName') ?>" required>
					</p>

					<p>
						<?php echo $account->getError(Constants::$emailsDoNotMatch); ?>
						<?php echo $account->getError(Constants::$emailInvalid); ?>
						<?php echo $account->getError(Constants::$emailTaken); ?>
						<label for="email">Email</label>
						<input id="email" name="email" type="email" placeholder="e.g. bart@gmail.com" value="<?php getInputValue('email') ?>" required>
					</p>

					<p>
						<label for="email2">Confirm email</label>
						<input id="email2" name="email2" type="email" placeholder="e.g. bart@gmail.com" value="<?php getInputValue('email2') ?>" required>
					</p>

					<p>
						<?php echo $account->getError(Constants::$passwordsDoNoMatch); ?>
						<?php echo $account->getError(Constants::$passwordNotAlphanumeric); ?>
						<?php echo $account->getError(Constants::$passwordCharacters); ?>
						<label for="password">Password</label>
						<input id="password" name="password" type="password" placeholder="Your password" required>
					</p>

					<p>
						<label for="password2">Confirm password</label>
						<input id="password2" name="password2" type="password" placeholder="Your password" required>
					</p>

					<button type="submit" name="registerButton">SIGN UP</button>

					<div class="hasAccountText">
						<span id="hideRegister">Already have an account? Log in here.</span>
					</div>
					
				</form>


			</div>

			<div id="loginText">
				<h1>Get great music, right now</h1>
				<h2>Listen to loads of songs for free</h2>
				<ul>
					<li>Discover music you'll fall in love with</li>
					<li>Create your own playlists</li>
					<li>Follow artists to keep up to date</li>
				</ul>
			</div>

		</div>
	</div>

</body>
</html>

var currentPlaylist = [];
var shufflePlaylist = [];
var tempPlaylist = [];
var audioElement;
var mouseDown = false;
var currentIndex = 0;
var repeat = false;
var shuffle = false;
var userLoggedIn;
var timer;

function openPage(url) {

	if(timer != null) {
		clearTimeout(timer);
	}

	if(url.indexOf("?") == -1) {
		url = url + "?";
	}

	var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
	console.log(encodedUrl);
	$("#mainContent").load(encodedUrl);
	$("body").scrollTop(0);
	history.pushState(null, null, url);
}

function createPlaylist() {
 
	var popup = prompt("Please enter the name of your playlist");
 
	if(popup != null) {
 
		$.post("includes/handlers/ajax/createPlaylist.php", { name: popup, username: userLoggedIn })
		.done(function(error) {
 
			if(error != "") {
				alert(error);
				return;
			}
 
			//do something when ajax returns
			openPage("yourMusic.php");
		});
 
	}
 
}


function formatTime(seconds) {
	var time = Math.round(seconds);
	var minutes = Math.floor(time / 60); //Rounds down
	var seconds = time - (minutes * 60);

	var extraZero = (seconds < 10) ? "0" : "";

	return minutes + ":" + extraZero + seconds;
}

function updateTimeProgressBar(audio) {
	$(".progressTime.current").text(formatTime(audio.currentTime));
	$(".progressTime.remaining").text(formatTime(audio.duration - audio.currentTime));

	var progress = audio.currentTime / audio.duration * 100;
	$(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio) {
	var volume = audio.volume * 100;
	$(".volumeBar .progress").css("width", volume + "%");
}

function playFirstSong() {
	setTrack(tempPlaylist[0], tempPlaylist, true);
}

function Audio() {

	this.currentlyPlaying;
	this.audio = document.createElement('audio');

	this.audio.addEventListener("ended", function() {
		nextSong();
	});

	this.audio.addEventListener("canplay", function() {
		//'this' refers to the object that the event was called on
		var duration = formatTime(this.duration);
		$(".progressTime.remaining").text(duration);
	});

	this.audio.addEventListener("timeupdate", function(){
		if(this.duration) {
			updateTimeProgressBar(this);
		}
	});

	this.audio.addEventListener("volumechange", function() {
		updateVolumeProgressBar(this);
	});

	this.setTrack = function(track) {
		this.currentlyPlaying = track;
		this.audio.src = track.path;
	}

	this.play = function() {
		this.audio.play();
	}

	this.pause = function() {
		this.audio.pause();
	}

	this.setTime = function(seconds) {
		this.audio.currentTime = seconds;
	}

}
                                
<?php

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
	include("includes/config.php");
	include("includes/classes/User.php");
	include("includes/classes/Artist.php");
	include("includes/classes/Album.php");
	include("includes/classes/Song.php");
	include("includes/classes/Playlist.php");

	if(isset($_GET['userLoggedIn'])) {
		$userLoggedIn = new User($con, $_GET['userLoggedIn']);
	}
	else {
		echo "Username variable was not passed into page. Check the openPage JS function";
		exit();
	}
}
else {
	include("includes/header.php");
	include("includes/footer.php");

	$url = $_SERVER['REQUEST_URI'];
	echo "<script>openPage('$url')</script>";
	exit();
}

?>
                                
                </div>


            </div>

        </div>

        <?php include("includes/nowPlayingBar.php"); ?>

    </div>

</body>

</html>


<?php
if(isset($_POST['loginButton'])) {
  //Login button was pressed
  $username = $_POST['loginUsername'];
  $password = $_POST['loginPassword'];

  $result = $account->login($username, $password);

  if($result == true) {
    $_SESSION['userLoggedIn'] = $username;
    header("Location: index.php");
  }

}
?>

<?php
	class User {

		private $con;
		private $username;

		public function __construct($con, $username) {
			$this->con = $con;
			$this->username = $username;
		}

		public function getUsername() {
			return $this->username;
		}

	}
?>

<?php
	class Song {

		private $con;
		private $id;
		private $mysqliData;
		private $title;
		private $artistId;
		private $albumId;
		private $genre;
		private $duration;
		private $path;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;

			$query = mysqli_query($this->con, "SELECT * FROM songs WHERE id='$this->id'");
			$this->mysqliData = mysqli_fetch_array($query);
			$this->title = $this->mysqliData['title'];
			$this->artistId = $this->mysqliData['artist'];
			$this->albumId = $this->mysqliData['album'];
			$this->genre = $this->mysqliData['genre'];
			$this->duration = $this->mysqliData['duration'];
			$this->path = $this->mysqliData['path'];
		}

		public function getTitle() {
			return $this->title;
		}

		public function getId() {
			return $this->id;
		}

		public function getArtist() {
			return new Artist($this->con, $this->artistId);
		}

		public function getAlbum() {
			return new Album($this->con, $this->albumId);
		}

		public function getPath() {
			return $this->path;
		}

		public function getDuration() {
			return $this->duration;
		}

		public function getMysqliData() {
			return $this->mysqliData;
		}

		public function getGenre() {
			return $this->genre;
		}

	}
?>

<?php
	class Album {

		private $con;
		private $id;
		private $title;
		private $artistId;
		private $genre;
		private $artworkPath;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;

			$query = mysqli_query($this->con, "SELECT * FROM albums WHERE id='$this->id'");
			$album = mysqli_fetch_array($query);

			$this->title = $album['title'];
			$this->artistId = $album['artist'];
			$this->genre = $album['genre'];
			$this->artworkPath = $album['artworkPath'];


		}

		public function getTitle() {
			return $this->title;
		}

		public function getArtist() {
			return new Artist($this->con, $this->artistId);
		}

		public function getGenre() {
			return $this->genre;
		}

		public function getArtworkPath() {
			return $this->artworkPath;
		}

		public function getNumberOfSongs() {
			$query = mysqli_query($this->con, "SELECT id FROM songs WHERE album='$this->id'");
			return mysqli_num_rows($query);
		}

		public function getSongIds() {

			$query = mysqli_query($this->con, "SELECT id FROM songs WHERE album='$this->id' ORDER BY albumOrder ASC");

			$array = array();

			while($row = mysqli_fetch_array($query)) {
				array_push($array, $row['id']);
			}

			return $array;

		}






	}
?>

<?php
include("../../config.php");

if(isset($_POST['name']) && isset($_POST['username'])) {

	$name = $_POST['name'];
	$username = $_POST['username'];
	$date = date("Y-m-d");

	$query = mysqli_query($con, "INSERT INTO playlists VALUES(null, '$name', '$username', '$date')");

}
else {
	echo "Name or username parameters not passed into file";
}

?>

<?php
include("includes/includedFiles.php");
?>

<div class="playlistsContainer">

	<div class="gridViewContainer">
		<h2>PLAYLISTS</h2>

		<div class="buttonItems">
			<button class="button green" onclick="createPlaylist()">NEW PLAYLIST</button>
		</div>



		<?php
			$username = $userLoggedIn->getUsername();

			$playlistsQuery = mysqli_query($con, "SELECT * FROM playlists WHERE owner='$username'");

			if(mysqli_num_rows($playlistsQuery) == 0) {
				echo "<span class='noResults'>You don't have any playlists yet.</span>";
			}

			while($row = mysqli_fetch_array($playlistsQuery)) {

				$playlist = new Playlist($con, $row);

				echo "<div class='gridViewItem' role='link' tabindex='0' 
							onclick='openPage(\"playlist.php?id=" . $playlist->getId() . "\")'>

						<div class='playlistImage'>
							<img src='assets/images/icons/playlist.png'>
						</div>
						
						<div class='gridViewInfo'>"
							. $playlist->getName() .
						"</div>

					</div>";



			}
		?>






	</div>

</div>

<?php
	class Playlist {

		private $con;
		private $id;
		private $name;
		private $owner;

		public function __construct($con, $data) {

			if(!is_array($data)) {
				//Data is an id (string)
				$query = mysqli_query($con, "SELECT * FROM playlists WHERE id='$data'");
				$data = mysqli_fetch_array($query);
			}

			$this->con = $con;
			$this->id = $data['id'];
			$this->name = $data['name'];
			$this->owner = $data['owner'];
		}

		public function getId() {
			return $this->id;
		}

		public function getName() {
			return $this->name;
		}

		public function getOwner() {
			return $this->owner;
		}

		public function getNumberOfSongs() {
			$query = mysqli_query($this->con, "SELECT songId FROM playlistSongs WHERE playlistId='$this->id'");
			return mysqli_num_rows($query);
		}

	}
?>

<?php
	class Artist {

		private $con;
		private $id;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;
		}

		public function getId() {
			return $this->id;
		}

		public function getName() {
			$artistQuery = mysqli_query($this->con, "SELECT name FROM artists WHERE id='$this->id'");
			$artist = mysqli_fetch_array($artistQuery);
			return $artist['name'];
		}
		
		public function getSongIds() {

			$query = mysqli_query($this->con, "SELECT id FROM songs WHERE artist='$this->id' ORDER BY plays ASC");

			$array = array();

			while($row = mysqli_fetch_array($query)) {
				array_push($array, $row['id']);
			}

			return $array;

		}
	}
?>

html, body {
    padding: 0;
    margin: 0;
    height: 100%;
}

* {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    color: #fff;
    letter-spacing: 1px;
}

body {
    background-color: #181818;
    font-size: 14px;
    min-width: 720px;
}

#nowPlayingBarContainer {
    width: 100%;
    background-color: #282828;
    bottom: 0;
    position: fixed;
    min-width: 620px;
}

#nowPlayingBar {
    display: flex;
    height: 90px;
    padding: 16px;
    box-sizing: border-box;
}

#nowPlayingLeft,
#nowPlayingRight {
    width: 30%;
    min-width: 180px;
}

#nowPlayingRight {
    position: relative;
    margin-top: 16px;
}

#nowPlayingCenter {
    width: 40%;
    max-width: 700px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

#nowPlayingBar .content {
    width: 100%;
    height: 57px;
}

.playerControls .buttons {
    margin: 0 auto;
    display: table;
}

.controlButton {
    background-color: transparent;
    border: none;
    vertical-align: middle;
}

.controlButton img {
    width: 20px;
    height: 20px;
}

.controlButton.play img,
.controlButton.pause img {
    width: 32px;
    height: 32px;
}

.controlButton:hover {
    cursor: pointer;
}

.progressTime {
    color: #a0a0a0;
    font-size: 11px;
    min-width: 40px;
    text-align: center;
}

.playbackBar {
    display: flex;
}

.progressBar {
    width: 100%;
    height: 12px;
    display: inline-flex;
    cursor: pointer;
}

.progressBarBg {
    background-color: #404040;
    height: 4px;
    width: 100%;
    border-radius: 2px;
}

.progress {
    background-color: #a0a0a0;
    height: 4px;
    width: 0;
    border-radius: 2px;
}

.playbackBar .progressBar {
    margin-top: 3px;
}

#nowPlayingLeft .albumArtwork {
    height: 100%;
    max-width: 57px;
    margin-right: 15px;
    float: left;
    background-size: cover;
}

#nowPlayingLeft .trackInfo {
    display: table;
}

#nowPlayingLeft .trackInfo .trackName {
    margin: 6px 0;
    display: inline-block;
    width: 100%;
}

#nowPlayingLeft .trackInfo .artistName span {
    font-size: 12px;
    color: #a0a0a0;
}

.volumeBar {
    width: 180px;
    position: absolute;
    right: 0;
}

.volumeBar .progressBar {
    width: 125px;
}

#topContainer {
    min-height: 100%;
    width: 100%;
}

#navBarContainer {
    background-color: #000;
    width: 220px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
}

.navBar {
    padding: 25px;
    display: flex;
    flex-direction: column;
    -ms-flex-direction: column;
}

.logo {
    margin-bottom: 15px;
}

.logo img {
    width: 32px;
}

.navBar .group {
    border-top: 1px solid #a0a0a0;
    padding: 10px 0;
}

.navItem {
    padding: 10px 0;
    font-size: 14px;
    font-weight: 700;
    display: block;
    letter-spacing: 1px;
    position: relative;
}

.navItemLink {
    color: #a0a0a0;
    text-decoration: none;
}

.navItemLink:hover {
    color: #ffffff;
}

.navItemLink .icon {
    position: absolute;
    right: 0;
    top: 6px;
    width: 25px;
}

#mainViewContainer {
    margin-left: 220px;
    padding-bottom: 90px;
    width: calc(100% - 220px);
}

#mainContent {
    padding: 0 20px;
}

.pageHeadingBig {
    padding: 20px;
    text-align: center;
}

.gridViewItem {
    display: inline-block;
    margin-right: 20px;
    width: 29%;
    max-width: 200px;
    min-width: 150px;
    margin-bottom: 20px;
}

.gridViewItem img {
    width: 100%;
}

.gridViewInfo {
    font-weight: 300;
    text-align: center;
    padding: 5px 0;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

.gridViewItem a {
    text-decoration: none;
}

.entityInfo {
    padding: 40px 0 10px 0;
    display: inline-block;
    width: 100%;
}

.entityInfo .leftSection {
    width: 30%;
    float: left;
    max-width: 250px;
}

.entityInfo .leftSection img {
    width: 100%;
}

.entityInfo .rightSection {
    width: 70%;
    float: left;
    padding: 5px 10px 5px 40px;
    box-sizing: border-box;
}

.entityInfo .rightSection h2 {
    margin-top: 0px;
}

.entityInfo .rightSection p {
    color: #939393;
    font-weight: 200;
}

.tracklist {
    padding: 0;
}

.tracklistRow {
    height: 40px;
    padding: 15px 10px;
    list-style: none;
}

.tracklistRow span {
    color: #939393;
    font-weight: 200;
}

.tracklistRow:hover {
    background-color: #282828;
}

.tracklistRow .trackCount {
    width: 8%;
    float: left;
}

.tracklistRow .trackCount img {
    width: 20px;
    visibility: hidden;
    position: absolute;
    cursor: pointer;
}

.tracklistRow:hover .trackCount img {
    visibility: visible;
}

.tracklistRow .trackCount span {
    visibility: visible;
}

.tracklistRow:hover .trackCount span {
    visibility: hidden;
}

.tracklistRow .trackInfo {
    width: 75%;
    float: left;
}

.tracklistRow .trackInfo span {
    display: block;
}

.tracklistRow .trackOptions {
    width: 5%;
    float: left;
    text-align: right;
}

.tracklistRow .trackOptions img {
    width: 15px;
    visibility: hidden;
}

.tracklistRow:hover .trackOptions img {
    visibility: visible;
}

.tracklistRow .trackDuration {
    width: 12%;
    float: left;
    text-align: right;
}

.tracklistRow .trackInfo .trackName {
    color: #fff;
    margin-bottom: 7px;
}

.artistInfo {
    text-align: center;
}

.button {
    color: #fff;
    cursor: pointer;
    margin-bottom: 10px;
    background-color: transparent;
    font-weight: 500;
    letter-spacing: 2px;
    border: 2px solid #fff;
    border-radius: 500px;
    padding: 15px;
    min-width: 130px;
}

.button.green {
    background-color: #2ebd59;
    border-color: #2ebd59;
}

.borderBottom {
    border-bottom: 1px solid #939393;
    margin-bottom: 20px;
}

.tracklistContainer h2,
.gridViewContainer h2,
.artistContainer h2 {
    text-align: center;
}

[role="link"] {
    cursor: pointer;
}

.searchContainer {
    background-color: #242424;
    padding: 20px 28px;
}

.searchContainer h4 {
    margin: 0;
    font-weight: 300;
}

.searchContainer .searchInput {
    width: 100%;
    background-color: transparent;
    border: none;
    color: #fff;
    font-weight: 700;
    font-size: 62px;
    letter-spacing: 2px;
}

input:focus {
    outline: none;
}

.searchResultRow {
    padding: 15px 10px;
}

.searchResultRow:hover {
    background-color: #292929;
}

.searchResultRow .artistName span {
    color:  #fff;
}

.noResults {
    margin:  15px;
    display: block;
}

.buttonItems .button {
    display: block;
    margin: 0 auto 20px auto;
}

.playlistsContainer {
    padding: 10px 0;
}

.playlistImage {
    border: 3px solid #282828;
    padding: 10px;
}

<?php include("includes/includedFiles.php"); 

if(isset($_GET['id'])) {
	$playlistId = $_GET['id'];
}
else {
	header("Location: index.php");
}

$playlist = new Playlist($con, $playlistId);
$owner = new User($con, $playlist->getOwner());
?>

<div class="entityInfo">

	<div class="leftSection">
		<img src="assets/images/icons/playlist.png">
	</div>

	<div class="rightSection">
		<h2><?php echo $playlist->getName(); ?></h2>
		<p>By <?php echo $playlist->getOwner(); ?></p>
		<p><?php echo $playlist->getNumberOfSongs(); ?> songs</p>
		<button class="button">DELETE PLAYLIST</button>

	</div>

</div>


<div class="tracklistContainer">
	<ul class="tracklist">
		
		<?php
		$songIdArray = array();//$album->getSongIds();

		$i = 1;
		foreach($songIdArray as $songId) {

			$albumSong = new Song($con, $songId);
			$albumArtist = $albumSong->getArtist();

			echo "<li class='tracklistRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<span class='trackNumber'>$i</span>
					</div>


					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>

					<div class='trackOptions'>
						<img class='optionsButton' src='assets/images/icons/more.png'>
					</div>

					<div class='trackDuration'>
						<span class='duration'>" . $albumSong->getDuration() . "</span>
					</div>


				</li>";

			$i = $i + 1;
		}

		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>

	</ul>
</div>

<?php 
include("includes/includedFiles.php"); 
?>


<script>openPage("browse.php")</script>

<?php 
include("includes/includedFiles.php"); 
?>

<h1 class="pageHeadingBig">You Might Also Like</h1>

<div class="gridViewContainer">

    <?php
        $albumQuery = mysqli_query($con, "SELECT * FROM albums ORDER BY RAND() LIMIT 10");

        while($row = mysqli_fetch_array($albumQuery)) {
            



            echo "<div class='gridViewItem'>
                    <span role='link' tabindex='0' onclick='openPage(\"album.php?id=" . $row['id'] . "\")'>
                        <img src='" . $row['artworkPath'] . "'>

                        <div class='gridViewInfo'>"
                            . $row['title'] .
                        "</div>
                    </span>

                </div>";



        }
    ?>

</div>

<?php include("includes/includedFiles.php"); 

if(isset($_GET['id'])) {
	$albumId = $_GET['id'];
}
else {
	header("Location: index.php");
}

$album = new Album($con, $albumId);
$artist = $album->getArtist();
$artistId = $artist->getId();
?>

<div class="entityInfo">

	<div class="leftSection">
		<img src="<?php echo $album->getArtworkPath(); ?>">
	</div>

	<div class="rightSection">
		<h2><?php echo $album->getTitle(); ?></h2>
		<p role="link" tabindex="0" onclick="openPage('artist.php?id=$artistId')">By <?php echo $artist->getName(); ?></p>
		<p><?php echo $album->getNumberOfSongs(); ?> songs</p>

	</div>

</div>


<div class="tracklistContainer">
	<ul class="tracklist">
		
		<?php
		$songIdArray = $album->getSongIds();

		$i = 1;
		foreach($songIdArray as $songId) {

			$albumSong = new Song($con, $songId);
			$albumArtist = $albumSong->getArtist();

			echo "<li class='tracklistRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<span class='trackNumber'>$i</span>
					</div>


					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>

					<div class='trackOptions'>
						<img class='optionsButton' src='assets/images/icons/more.png'>
					</div>

					<div class='trackDuration'>
						<span class='duration'>" . $albumSong->getDuration() . "</span>
					</div>


				</li>";

			$i = $i + 1;
		}

		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>

	</ul>
</div>

<?php
include("includes/config.php");
include("includes/classes/Artist.php");
include("includes/classes/Album.php");
include("includes/classes/Song.php");

//session_destroy(); LOGOUT

if(isset($_SESSION['userLoggedIn'])) {
    $userLoggedIn = $_SESSION['userLoggedIn'];
    echo "<script>userLoggedIn = '$userLoggedIn';</script>";
}
else {
    header("Location: register.php");
}

?>

<html>
<head>
    <title>Welcome to Slotify!</title>

    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
</head>

<body>

    <div id="mainContainer">

        <div id="topContainer">

            <?php include("includes/navBarContainer.php"); ?>

            <div id="mainViewContainer">

                <div id="mainContent">
                  
<!-- Ended at 6:00 6-22-2022 -->
