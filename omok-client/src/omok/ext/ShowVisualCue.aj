package omok.ext;

import omok.base.BoardPanel;
import omok.base.ColorPlayer;
import omok.base.OmokDialog;
import omok.model.Board;
import omok.model.Player;

import java.awt.*;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;

/**
 * Show Visual Cue Aspect
 *
 * Adds a visual cue to the game that outlines the position of the player's cursor
 * when they highlight a valid cell.This highlight contrasts the color of the player's
 * stone color.
 */
privileged public aspect ShowVisualCue {

    /// POINTCUTS

    pointcut createBoardPanel(OmokDialog d) : this(d) && execution(BoardPanel OmokDialog.createBoardPanel(..));
    pointcut paint(BoardPanel panel, Graphics g) : this(panel) && args(g) && execution(void BoardPanel.paint(Graphics));


    /// ADVICE

    after(BoardPanel panel, Graphics g) : paint(panel, g) {
        try {
            Board board = panel.board;

            // draw cheat locations
            if (board.cheatModeOn()) {
                if (board.hasOpponentMoves()) {
                    g.setColor(Color.RED);
                    Iterable<Board.Place> bestMoves = board.opponentMoves();
                    for (Board.Place p : bestMoves) {
                        if (p.isEmpty()) panel.drawHighlight(g, p);
                    }
                }
                if (board.hasPlayerMoves()) {
                    g.setColor(Color.BLUE);
                    Iterable<Board.Place> bestMoves = board.playerMoves();
                    for (Board.Place p : bestMoves) {
                        if (p.isEmpty()) panel.drawHighlight(g, p);
                    }
                }
                // board.potentialWins()
                // board.potentialLosses()
            }

            // draws mouse location
            Board.Place mouseLocation = panel.mouseLocation;
            Color mouseColor = panel.mouseColor;

            if (mouseLocation != null && mouseColor != null && !board.isGameOver()) {
                g.setColor(mouseColor);
                panel.drawStone(g, mouseLocation);
            }

            // draws win
            if (board.isGameOver() && !board.isFull()) {
                g.setColor(Color.GREEN);
                Iterable<Board.Place> winningRow = board.winningRow();
                for (Board.Place p : winningRow) {
                    panel.drawHighlight(g, p);
                }
            }
        }
        catch (Exception e) {
            e.printStackTrace();
        }
    }

    /*
     * Creates a mouse listener that updates the mouseLocation and mouseColor properties within the BoardPanel. This
     * state is used by BoardPanel#paint(Graphics) to draw a stone when the player highlights a place with the mouse
     * pointer.
     */
    after(OmokDialog d) returning(BoardPanel panel) : createBoardPanel(d) {

        panel.addMouseMotionListener(new MouseAdapter() {
            @Override
            public void mouseMoved(MouseEvent mouseEvent) {
                Color color = null;

                panel.setMouseLocation(mouseEvent.getX(), mouseEvent.getY());

                Player p = d.getPlayer();

                if (p instanceof ColorPlayer) {
                    color = ((ColorPlayer)p).color();
                }

                panel.setMouseColor(color);
                d.repaint();

            }
        });
    }


    /// INTER-TYPE DECLARATION

    private Board.Place BoardPanel.mouseLocation;
    private Color BoardPanel.mouseColor;

    void BoardPanel.setMouseLocation(int x, int y) {
        Board board = this.board;
        Board.Place place = locatePlace(x, y);

        if (place == null || board.isOccupied(place.x, place.y)) this.mouseLocation = null;
        else this.mouseLocation = locatePlace(x, y);
    }


    void BoardPanel.setMouseColor(Color mouseColor) {
        this.mouseColor = mouseColor;
    }

    /** Draw a highlight at the location of the given place. */
    private void BoardPanel.drawHighlight(Graphics g, Board.Place p) {
        int reducer = 12;

        int x = placeSize + p.x * placeSize; // center x
        int y = placeSize + p.y * placeSize; // center y
        g.fillOval(x - (reducer / 2), y - (reducer / 2), reducer, reducer);
    }
}
