package omok.ext;

import omok.base.ColorPlayer;
import omok.base.OmokDialog;
import omok.model.Board;
import omok.model.Player;
import java.awt.Color;
import java.util.ArrayList;
import java.util.List;

/**
 * Add Opponent Aspect
 *
 * Modifies the game to support two player mode. Adds a new player(white) and alternates
 * that player's stone placement with the first player(black).
 */
public privileged aspect AddOpponent {

    /// POINTCUTS

    pointcut OmokDialog(OmokDialog d) : this(d) && initialization(OmokDialog.new());


    /// ADVICE

    after (OmokDialog d) : OmokDialog(d) {

        d.players.add(0, d.player);
        d.players.add(1, new ColorPlayer("White", Color.WHITE));

        d.board.addBoardChangeListener(new Board.BoardChangeAdapter() {
            @Override
            public void stonePlaced(Board.Place place) {
                d.nextPlayer();
                d.showMessagePublic(d.getPlayer().name() + "'s turn.");
            }
        });

        d.players.get(0).audioPath = "Trump_says_CHINA.wav";
        d.players.get(1).audioPath = "Hillary_Clinton_says_baghdaddy.wav";
    }


    /// INTER-TYPE DECLARATIONS

    private String Player.audioPath;

    public String Player.getAudioPath() {
        return this.audioPath;
    }


    private ArrayList<Player> OmokDialog.players = new ArrayList<>();

    public void OmokDialog.setPlayer(Player player) {
        this.player = player;
    }

    public Player OmokDialog.getPlayer() {
        return this.player;
    }

    public List<Player> OmokDialog.getPlayers() {
        return this.players;
    }

    public void OmokDialog.showMessagePublic(String message) {
        this.showMessage(message);
    }

    /**
     * Sets the player property to the next player within the players {@link List}.
     *
     * @see Player
     */
    void OmokDialog.nextPlayer() {

        int index = this.players.indexOf(player);
        int size = players.size();

        if (index == -1) return;
        if (index == size - 1) player = players.get(0);
        else player = players.get(index + 1);
    }

    Player OmokDialog.getOpponent() {
        int index = this.players.indexOf(player);
        int size = players.size();

        if (index == -1) return null;
        if (index == size - 1) return players.get(0);
        else return players.get(index + 1);
    }
}
