//$Id: Player.java,v 1.1 2013/02/02 18:51:22 cheon Exp $

package omok.base;

import java.awt.Color;

import omok.model.Player;

/**
 * An player who knows his or her stone color.
 *
 * @author Yoonsik Cheon
 */
public class ColorPlayer extends Player {

    /** Color of this player's stone. */
    private final Color color;

    /**
     * Create a new player with given name. The created player will
     * have stones of the given color.
     */
    public ColorPlayer(String name, Color color) {
        super(name);
        this.color = color;
    }

    /** Returns this player's stone color. */
    public Color color() {
        return color;
    }
}
