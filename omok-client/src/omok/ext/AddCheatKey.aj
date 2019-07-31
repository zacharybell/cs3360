package omok.ext;

import omok.base.BoardPanel;
import omok.base.OmokDialog;
import omok.model.Board;
import omok.model.Player;

import javax.swing.*;
import java.awt.event.ActionEvent;
import java.awt.event.KeyEvent;
import java.util.Set;
import java.util.*;

/**
 * Cheat Code Aspect
 *
 * Enables a cheat mode with F5 that hints a winning row and warns loosing rows. A
 * winning/loosing row is a row that can win the game with one more move, e.g. an
 * open sequence of four consecutive stones of the same color. When a winning/loosing
 * row exists, that cell is highlighted for the user.
 *
 * Requirements:
 *  - Detects any cell adjacent to a winning or loosing row
 *  - Highlights detected cell
 *
 * Bonus:
 *  - Detects a empty embedded place ex. XOOO_OX or _O_OO_
 */
public privileged aspect AddCheatKey {

    /// POINTCUTS
    pointcut BoardPanel(BoardPanel panel) : this(panel) && initialization(BoardPanel.new(..));
    pointcut makeMove(OmokDialog d, Board.Place p) : this(d) && args(p) && execution(void OmokDialog.makeMove(..));

    /// ADVICE

    /*
     * Updates the playerMoves (cheat for win) and opponentMoves (cheat for block) sets within Board that contain all
     * of the moves that should be highlighted if the cheat key is enabled.
     */
    after(OmokDialog d, Board.Place p) : makeMove(d, p) {
        Iterable<Board.Place> places = d.board.places();
        d.board.clearBestMoves();
        for (Board.Place place : places) {
            if (!place.isEmpty()) {
                d.board.updateBestMoves(d, place, 4);
            }
        }
    }

    /*
     * Sets up the F5 key listener and uses it to toggle the Board's cheatEnabled property
     */
    after(BoardPanel panel) : BoardPanel(panel) {
        Board board = panel.board;
        ActionMap map = panel.getActionMap();
        int condition = JComponent.WHEN_IN_FOCUSED_WINDOW;
        InputMap inputMap = panel.getInputMap(condition);
        String reload = "Cheat";
        inputMap.put(KeyStroke.getKeyStroke(KeyEvent.VK_F5, 0), reload);
        map.put(reload, new KeyAction(panel, reload) {
            @Override
            public void actionPerformed(ActionEvent actionEvent) {
                if (actionEvent.getActionCommand().equals(reload)) {
                    board.toggleCheatMode();
                }
            }
        });
    }

    @SuppressWarnings("serial")
    private abstract static class KeyAction extends AbstractAction {
        private final BoardPanel boardPanel;

        private KeyAction(BoardPanel boardPanel, String command) {
            this.boardPanel = boardPanel;
            putValue(ACTION_COMMAND_KEY, command);
        }
    }





    /// INTER-TYPE DECLARATIONS - Board

    private Set<Board.Place> Board.playerMoves = new HashSet<>();
    private Set<Board.Place> Board.opponentMoves = new HashSet<>();

    private boolean Board.cheatMode = false;

    boolean Board.hasPlayerMoves() {
        return playerMoves.size() > 0;
    }

    boolean Board.hasOpponentMoves() {
        return opponentMoves.size() > 0;
    }

    Iterable<Board.Place> Board.playerMoves() {
        return playerMoves;
    }

    Iterable<Board.Place> Board.opponentMoves() {
        return opponentMoves;
    }

    void Board.toggleCheatMode() {
        if (cheatMode) this.cheatMode = false;
        else this.cheatMode = true;
    }

    void Board.clearBestMoves() {
        playerMoves.clear();
        opponentMoves.clear();
    }

    boolean Board.cheatModeOn() {
        return this.cheatMode;
    }

    /**
     * Updates the {@link Board#playerMoves} and {@link Board#opponentMoves} sets.
     *
     * @param d
     * @param p
     * @param length
     */
    void Board.updateBestMoves(OmokDialog d, Board.Place p, int length) {

        Player player = d.getPlayer();

        updateBestMoves(player, p.x, p.y, 1, 0, length, false);
        updateBestMoves(player, p.x, p.y, 0, 1, length, false);
        updateBestMoves(player, p.x, p.y, 1, 1, length, false);
        updateBestMoves(player, p.x, p.y, 1, -1, length, false);

        Player opponent = d.getOpponent();

        updateBestMoves(opponent, p.x, p.y, 1, 0, length, true);
        updateBestMoves(opponent, p.x, p.y, 0, 1, length, true);
        updateBestMoves(opponent, p.x, p.y, 1, 1, length, true);
        updateBestMoves(opponent, p.x, p.y, 1, -1, length, true);
    }

    /**
     * Updates the player's move conditions based on whether that player is the current focused player or his/her
     * opponent.
     *
     * @param player
     * @param x
     * @param y
     * @param dx
     * @param dy
     * @param length
     * @param opponent true if this is the opponent
     * @return
     */
    boolean Board.updateBestMoves(Player player, int x, int y, int dx, int dy, int length, boolean opponent) {
        List<Board.Place> buffer = new ArrayList<>();

        int count = 0;
        boolean hole = false;
        boolean mulligan = false;

        int blocked = 0;


        int sx = x;  // starting x and y
        int sy = y;  // i.e., (sx, sy) <----- (x,y)
        while (!(dx > 0 && sx < 0) && !(dx < 0 && sx >= size)
                && !(dy > 0 && sy < 0) && !(dy < 0 && sy >= size)
                && count <= length) {
            // detects an opposing player (X)OOO_ condition
            if (!isOccupiedBy(sx, sy, player) && isOccupied(sx, sy)) {
                blocked++;
                break;
            }
            // a middle space has already been counted OO_O(_) and another empty space is hit
            if (isEmpty(sx, sy) && mulligan) {
                buffer.add(at(sx, sy));
                break;
            }
            // second empty space hit
            if (isEmpty(sx, sy) && hole) break;


            // first empty space hit
            if (isEmpty(sx, sy) && !hole) {
                buffer.add(at(sx, sy));
                hole = true;
            }
            // another player space detected satisfying OO_O condition
            else if (isOccupiedBy(sx, sy, player) && hole) {
                hole = false;
                mulligan = true;    // you get only one!
                count += 2;
            }
            // occupied by player
            else {
                count++;
            }

            //buffer.add(at(sx, sy));
            sx -= dx;
            sy -= dy;
        }

        // check right/higher side of (x,y)
        int ex = x + dx; // ending x and y
        int ey = y + dy; // i.e., (x,y) -----> (ex, ey)

        // resets hole if the first checked place has a player
        if ((!(dx > 0 && ex >= size) && !(dx < 0 && ex < 0)
                && !(dy > 0 && ey >= size) && !(dy < 0 && ey < 0)
                && count < length) && isOccupiedBy(ex, ey, player))
        {
            hole = false;
        }

        while (!(dx > 0 && ex >= size) && !(dx < 0 && ex < 0)
                && !(dy > 0 && ey >= size) && !(dy < 0 && ey < 0)
                && count <= length) {
            // detects an opposing player (X)OOO_ condition
            if (!isOccupiedBy(ex, ey, player) && isOccupied(ex, ey)) {
                blocked++;
                break;
            }
            // a middle space has already been counted OO_O(_) and another empty space is hit
            if (isEmpty(ex, ey) && mulligan) {
                buffer.add(at(ex, ey));
                //count++;
                break;
            }
            // second empty space hit
            if (isEmpty(ex, ey) && hole) break;

            // first empty space hit
            if (isEmpty(ex, ey) && !hole) {
                buffer.add(at(ex, ey));
                hole = true;
            }
            // another player space detected satisfying OO_O condition
            else if (isOccupiedBy(ex, ey, player) && hole) {
                hole = false;
                mulligan = true;    // you get only one!
                count += 2;
            }
            // occupied by player
            else {
                count++;
            }

            ex += dx;
            ey += dy;
        }

        if ((count == length && blocked != 2) || (count > length)) {
            if (opponent) opponentMoves.addAll(buffer);
            else playerMoves.addAll(buffer);
            return true;
        }
        return false;
    }
}
