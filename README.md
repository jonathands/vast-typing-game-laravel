## Typing Game Vue Frontend - Challenge

This is a pure SPA, Vue + Vite project, consuming a Laravel backend. I'm using Vue Router and structuring the project into Services, Pages, and Components, trying to keep a good abstraction/complexity ratio for the project size. Pinia stores are used throughout the project in tandem with the Services, which are basically a data consumption layer using Axios.

This game was designed to be playable without registration, but requires registration for ranking tracking.  
All data is available without authentication for visualization.

The typing challenge interface avoids HTML components on purpose and directly tracks keystrokes from the user with its internal state.


To manually test the backend a Bruno Rest Client collection is provided, 

### What is missing

* Game logic validation: the game must submit each word.  
* Frontend game integrity validation: checking for some statistical aberration before submission.  
* User registration prompting: if the user is not authenticated, it should be visible in the game screen and the user should be prompted to register upon finishing a typing challenge.

## AI Disclosure

### What was made manually

* Project setup and initial Page/Component structure  
* Base CSS and color choices  
* Basic wiring for the backend  
* Structuring services and stores  
* Initial type definitions

### What was done with AI

* Per-component styling with Tailwind  
* Services refactoring and basic game logic  
* Type revision to guarantee completeness  
* Lots of debugging (replacing Google and Stack Overflow :)  
* Very basic test generation

## Project Setup

```sh
npm install
