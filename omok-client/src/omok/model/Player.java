//$Id: Player.java,v 1.1 2013/02/02 18:51:22 cheon Exp $

package omok.model;

/**
 * A player of the omok game. Each player has a name.
 *
 * @author Yoonsik Cheon
 */
public class Player {

    /** Name of this player. */
    private final String name;

    /** Create a new player of the given name. */
    public Player(String name) {
        this.name = name;
    }

    /** Returns the name of this player. */
    public String name() {
        return name;
    }
}
