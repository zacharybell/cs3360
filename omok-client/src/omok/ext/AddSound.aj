package omok.ext;

import omok.base.BoardPanel;
import omok.base.OmokDialog;
import java.io.IOException;
import javax.sound.sampled.AudioInputStream;
import javax.sound.sampled.AudioSystem;
import javax.sound.sampled.Clip;
import javax.sound.sampled.LineUnavailableException;
import javax.sound.sampled.UnsupportedAudioFileException;

import javax.sound.sampled.*;

public privileged aspect AddSound {

	pointcut OmokDialog(OmokDialog d) : this(d) && initialization(OmokDialog.new());
	pointcut makeMove(OmokDialog d) : this(d) && execution(void OmokDialog.makeMove(..));

	after (OmokDialog d) : makeMove(d){
		/** Directory where audio files are stored. */
	    final String SOUND_DIR = "/sounds/";

		/** Play the given audio file. Inefficient because a file will be
		 * (re)loaded each time it is played. */
		if (!d.board.isGameOver()) {
			try {
				AudioInputStream audioIn = AudioSystem.getAudioInputStream(
						OmokDialog.class.getResource(SOUND_DIR + d.getPlayer().getAudioPath()));

				DataLine.Info info = new DataLine.Info(Clip.class, audioIn.getFormat());
				Clip clip = (Clip) AudioSystem.getLine(info);
				clip.open(audioIn);
				clip.start();
			} catch (UnsupportedAudioFileException | IOException | LineUnavailableException e) {
				e.printStackTrace();
			}
		}
	}

	after() : execution(void gameOver(..)){
		/** Directory where audio files are stored. */
		final String SOUND_DIR = "/sounds/";

		/** Play the given audio file. Inefficient because a file will be
		 * (re)loaded each time it is played. */
		try {
			AudioInputStream audioIn = AudioSystem.getAudioInputStream(
					OmokDialog.class.getResource(SOUND_DIR + "Final_Fantasy_VII-Victory_Fanfare.wav"));
			DataLine.Info info = new DataLine.Info(Clip.class, audioIn.getFormat());
			Clip clip = (Clip)AudioSystem.getLine(info);
			clip.open(audioIn);
			clip.start();
		} catch (UnsupportedAudioFileException | IOException | LineUnavailableException e) {
			e.printStackTrace();
		}
	}
}
