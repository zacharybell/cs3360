package omok.model;

import java.util.*;

/**
 * Abstraction of an omok board. An omok board consists of nxn 
 * intersections or places on which players can place their stones.
 * The places of a board are denoted by 0-based indices x and y,
 * where x and y are column and row indices. The top left
 * place is (0,0) and the bottom right place is (n-1,n-1).
 *
 * @see Board.Place
 *
 * @author Yoonsik Cheon
 */
public class Board {
    
    /** Default board size. It is 15. */
    private static final int DEFAULT_BOARD_SIZE = 15;

    /**
     * Number of rows/columns of this board. This board has 
     * <code>size x size</code> places/intersections. 
     */
    private final int size;
    // inv: size == Math.sqrt(places.size())

    /** Places of this board. */
    private final List<Place> places;

    /** Winning sequence of places. */
    private List<Place> winningRow = new ArrayList<>(0);
    
    /** Listeners to be notified for board changes such as placing
     * stones. */
    private final List<BoardChangeListener> listeners;

    /** Create a new board of default size. */
    public Board() {
        this(DEFAULT_BOARD_SIZE);
    }
    
    /** Create a new board of the given size. */
    public Board(int size) {
        this.size = size;
        listeners = new ArrayList<>();
        places = new ArrayList<>(size * size);
        for (int x = 0; x < size; x++) {
            for (int y = 0; y < size; y++) {
                places.add(new Place(x, y));
            }
        }
    }
    
    /** Clear this board by removing all its stones. */
    public void clear() {
        winningRow.clear();
        places.forEach(p -> p.clear());
    }
    
    /** Return the size of this board. */
    public int size() {
        return size;
    }

    /** Do all places of this board have stones placed? */
    public boolean isFull() {
        return places.stream().allMatch(p -> p.isOccupied());
    }

    /** Is the game over? */
    public boolean isGameOver() {
        return winningRow.size() > 0 || isFull();
    }
    
    /**
     * Place a stone of the given player at the specified place.
     * The specified place is assumed to be empty; if not, this
     * method has no effect.
     *
     * @param x 0-based column (vertical) index
     * @param y 0-based row (horizontal) index
     */
    public void placeStone(int x, int y, Player player) {
        Place place = at(x, y);
        if (place != null) {
            place.setOccupant(player);
            notifyStonePlaced(place);
            if (isWonBy(player)) {
                notifyGameOver(player); // win
            } else if (isFull()) {
                notifyGameOver(null);   // draw
            }
        }
    }
    
    /** Return the specified place or null if it doesn't exist. */ 
    public Place at(int x, int y) {
        return places.stream()
                .filter(p -> p.x == x && p.y == y).findAny().get();
    }
    
    /** Return the places of this board. */
    public Iterable<Place> places() {
        return places;
    }
    
    /**
     * Is the specified place empty? 
     *
     * @param x 0-based column (vertical) index
     * @param y 0-based row (horizontal) index
     */
    public boolean isEmpty(int x, int y) {
        return at(x,y).isEmpty();
    }

    /**
     * Is the specified place occupied? 
     *
     * @param x 0-based column (vertical) index
     * @param y 0-based row (horizontal) index
     */
    public boolean isOccupied(int x, int y) {
        return at(x,y).isOccupied();
    }
    
    /**
     * Is the specified place occupied by the given player?
     *
     * @param x 0-based column (vertical) index
     * @param y 0-based row (horizontal) index
     */
    public boolean isOccupiedBy(int x, int y, Player player) {
        return at(x,y).occupant() == player;
    }
    
    /**
     * Return the player occupying the specified place; null if the place
     * is empty (has no stone). 
     *
     * @param x 0-based column (vertical) index
     * @param y 0-based row (horizontal) index
     */
    public Player playerAt(int x, int y) {
        return at(x,y).occupant();
    }

    /** Return true if the given player has a winning row. */
    public boolean isWonBy(Player player) {
        return places.stream().anyMatch(p -> 
                isWonBy(p.x, p.y, 1, 0, player)       // horizontal
                || isWonBy(p.x, p.y, 0, 1, player)    // vertical
                || isWonBy(p.x, p.y, 1, 1, player)    // diagonal(\)
                || isWonBy(p.x, p.y, 1, -1, player)); // diagonal(/)
    }

    /** Return the winning row. */
    public Iterable<Place> winningRow() {
        return winningRow;
    }

    /** Return true if the given player has a winning row containing 
     * the specified place in the specified direction, where a direction
     * is represented as:
     * <ul>
     * <li> horizontal: dx = 1, dy = 0</li>
     * <li> vertical: dx = 0, dy = 1</li>
     * <li> diagonal (\): dx = 1, dy = 1</li>
     * <li> diagonal (/): dx = 1, dy = -1</li>
     * </ul>
     */
    private boolean isWonBy(int x, int y, int dx, int dy, Player player) {
        // consecutive places occupied by the given player
        final List<Place> row = new ArrayList<>(5);
        
        // check left/lower side of (x,y)
        int sx = x;  // starting x and y
        int sy = y;  // i.e., (sx, sy) <----- (x,y)
        while (!(dx > 0 && sx < 0) && !(dx < 0 && sx >= size) 
                && !(dy > 0 && sy < 0) && !(dy < 0 && sy >= size) 
                && isOccupiedBy(sx, sy, player) && row.size() < 5) {
            row.add(at(sx, sy));
            sx -= dx;
            sy -= dy;
        }
        
        // check right/higher side of (x,y)
        int ex = x + dx; // ending x and y
        int ey = y + dy; // i.e., (x,y) -----> (ex, ey)
        while (!(dx > 0 && ex >= size) && !(dx < 0 && ex < 0) 
                && !(dy > 0 && ey >= size) && !(dy < 0 && ey < 0) 
                && isOccupiedBy(ex, ey, player) && row.size() < 5) {
            row.add(at(ex, ey));
            ex += dx;
            ey += dy;
        }
        
        if (row.size() >= 5) {
            winningRow = row;
        }
        return row.size() >= 5;
    }

    /** Register the given listener for board changes. */
    public void addBoardChangeListener(BoardChangeListener listener) {
        if (!listeners.contains(listener)) {
            listeners.add(listener);
        }
    }
    
    /** Unregister the given listener. */
    public void removeBoardChangeListener(BoardChangeListener listener) {
        listeners.remove(listener);
    }
    
    /** Notify to registered listeners when a stone is placed. */
    private void notifyStonePlaced(Place place) {
        for (BoardChangeListener listener: listeners) {
            listener.stonePlaced(place);
        }
    }
    
    /** Notify to registered listeners when the game is over. The given
     * player is the winner; it is null for a draw. */
    private void notifyGameOver(Player player) {
        for (BoardChangeListener listener: listeners) {
            listener.gameOver(player);
        }
    }
    
    /** To listen to board changes such as placing stones. */
    public interface BoardChangeListener {
        /** Called when a stone is placed. */
        void stonePlaced(Place place);
        
        /** Called when the game is over.
         * @param winner The winner or null for a draw. */
        void gameOver(Player winner);
    }

    /** Adapter with default implementation. */
    public static class BoardChangeAdapter implements BoardChangeListener {
        public void stonePlaced(Place place) {}
        public void gameOver(Player winner) {}
    }
    
    /**
     * A place of a board that knows its 0-based x (column) 
     * and y (row) coordinates and the placed stone (occupant).
     * 
     * @author Yoonsik Cheon
     * 
     * @see Board
     * @see Player
     */
    public static class Place {
        
        /** 0-based column coordinate of this place. */
        public final int x;

        /** 0-based row coordinate of this place. */
        public final int y;

        /** Player who placed a stone on this place; null
         * if empty. */
        private Player occupant;

        /** Create a new place of the given coordinates. 
         * 
         * @param x 0-based column (vertical) coordinate
         * @param y 0-based row (horizontal) coordinate
         */
        private Place(int x, int y) {
            this.x = x;
            this.y = y;
        }
        
        /** Remove the occupant of this place. */
        private void clear() {
            occupant = null;
        }
        
        /** Set the occupant of this place. */
        private void setOccupant(Player player) {
            this.occupant = player;
        }
        
        /** Return the owner of the stone placed on this place. */
        public Player occupant() {
            return occupant;
        }
        
        /** Does this place have a stone placed? */
        public boolean isOccupied() {
            return occupant != null;
        }
        
        /** Does this place have no stone placed? */
        public boolean isEmpty() {
            return occupant == null;
        }
    }
}
