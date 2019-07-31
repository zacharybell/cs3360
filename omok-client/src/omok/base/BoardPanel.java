// $Id: OmokCanvas.java,v 1.3 2013/02/06 02:57:44 cheon Exp $

package omok.base;

import java.awt.Color;
import java.awt.Graphics;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;

import javax.swing.JPanel;

import omok.model.Board;

/**
 * A special panel to display an omok board modeled by the
 * {@link omok.model.Board} class. An omok board is displayed as
 * a 2D grid, and stones are displayed as filled circles.
 *
 * @see omok.model.Board
 * @author Yoonsik Cheon
 */
@SuppressWarnings("serial")
public class BoardPanel extends JPanel {
    
    /** Callback interface to listen for board click events. */
    public interface BoardPanelListener {

        /** Called when an unoccupied place is selected. 
         * 
         * @param place The selected place
         */
        void placeSelected(Board.Place place);
    }
    
    /** Number of pixels between horizontal/vertical lines of the board. */
    private int placeSize = 20;

    /** Background color of the board. */
    private Color boardColor = new Color(245, 184, 0);
    
    /** Board to display. */
    private Board board;

    /** Create a new board panel to display the given board. */
    public BoardPanel(final Board board, final BoardPanelListener listener) {
        this.board = board;
        addMouseListener(new MouseAdapter() {
            public void mouseClicked(MouseEvent e) {
                Board.Place place = locatePlace(e.getX(), e.getY());
                if (place != null && !place.isOccupied()) {
                    listener.placeSelected(place);
                }
            }
        });
    }
       
    /** Overridden here to draw the board along with placed stones. */
    @Override
    public void paint(Graphics g) {
        super.paint(g); // clear the background
        drawGrid(g);
        drawStones(g);
    }
  
    /** Draw a 2D grid representing the board. */ 
    private void drawGrid(Graphics g) {
        // number of v/h lines including boarder lines
        final int lines = board.size() + 2;

        // paint background
        final Color oldColor = g.getColor();
        g.setColor(boardColor);
        g.fillRect(0, 0, placeSize * (lines - 1), placeSize * (lines - 1));

        // draw vertical lines
        g.setColor(Color.BLACK);        
        int x = 0; //placeSize;
        for (int i = 0; i < lines; i++) {
            g.drawLine(x, 0, x, placeSize * (lines - 1));
            x += placeSize;
        }

        // draw horizontal lines
        int y = 0; //placeSize;
        for (int i = 0; i < lines; i++) {
            g.drawLine(0, y, placeSize * (lines - 1), y);
            y += placeSize;
        }
        g.setColor(oldColor);
    }

    /** Draw stones placed on the board. Stones are displayed as filled
     *  circles. */
    private void drawStones(Graphics g) {
        for (Board.Place p: board.places()) {
            if (p.isOccupied()) {
                g.setColor(((ColorPlayer) p.occupant()).color());
                drawStone(g, p);
            }
        }
    }
    
    /** Draw a stone on the given place. */
    private void drawStone(Graphics g, Board.Place p) {
        int x = placeSize + p.x * placeSize; // center x
        int y = placeSize + p.y * placeSize; // center y
        int r = placeSize / 2;               // radius
        g.fillOval(x - r, y - r, r * 2, r * 2);   
    }
 
    /**
     * Given a screen coordinate, return the corresponding place of 
     * the board; return null if it doesn't correspond to any place. 
     */
    private Board.Place locatePlace(int x, int y) {
        // Panel
        //
        // X---X---X-- O: placeable
        // |PS |   |   X: Not placeable
        // X---O---O--
        //
        final int boardSize = board.size();
        final int boarder = placeSize; // * 1;
        // recognize R pixels from an intersection
        final int R = placeSize / 2 - 2; 
        
        // off board?
        if (x < boarder - R || y < boarder - R
                || x > placeSize * (boardSize + 1) - (placeSize - R)
                || y > placeSize * (boardSize + 1) - (placeSize - R)) {
            return null;
        }

        int px = 0;
        // -+-R)--(-R-+-
        int dx = (x - boarder) % placeSize;
        if (dx <= R) {
            px = (x - boarder) / placeSize;
        } else if (dx >= placeSize - R) {
            px = (x - boarder) / placeSize + 1;
        } else {
            return null;
        }

        int py = 0;
        int dy = (y - boarder) % placeSize;
        if (dy <= R) {
            py = (y - boarder) / placeSize;
        } else if (dy >= placeSize - R) {
            py = (y - boarder) / placeSize + 1;
        } else {
            return null;
        }
        return board.at(px, py);
    }
    
}
