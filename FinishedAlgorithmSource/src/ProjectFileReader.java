import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;


public class ProjectFileReader {
    /**
     * Bufferedreader des Dokumentes
     */
    private BufferedReader reader;
    /**
     * Liste aller Projekte, die gelesen wurden
     */
    private ArrayList<Projekt> projektList;
    /**
     * String zur Festlegung des Aufbaus der CSV-Datei. Beispielsweise "I;Mm" , dafuer, dass in der ersten Spalte die IDs der Projekte stehen, die zweite Spalte uebersprungen wird, die 3Spalte die maximal Anzahl der Teilnehmer enthaelt und die 4.Spalte die minimal Anzahl der Teilnehmer fuer das Projekt enthaelt.
     */
    private String regex;

    /**
     * Trennzeichen der CSV-Datei
     */
    private String delimiter;

    /**
     * Spaltenindex in der CSV-Datei, der angibt, welche ID ein Projekt hat
     */
    private int idIndex;

    /**
     * Spaltenindex in der CSV-Datei, der angibt, welche Mindestanzahl an Schülern ein Projekt hat
     */
    private int minIndex;

    /**
     * Spaltenindex in der CSV-Datei, der angibt, welche Maximalanzahl an Schülern ein Projekt hat
     */
    private int maxIndex;

    /**
     * Klasse zum Auslesen einer CSV-Datei nach Projekten
     * Initialisiert BufferedReader
     *
     * @param source Path zu der Datei
     * @param regex  String zur Festlegung des Aufbaus der CSV-Datei
     */
    public ProjectFileReader(String source, String regex) {
        try {
            this.regex = regex;
            this.validateRegex();
            this.reader = new BufferedReader(new FileReader(source));
        } catch (Exception e) {
            this.reader = null;
            e.printStackTrace();
            System.exit(0);
        }
    }

    /**
     * Validiert die Beschreibung der CSV-Datei
     */
    public void validateRegex() {
        if (this.regex.length() == 0) {
            System.out.println("Ungültige Beschreibung der CSV-Datei, zu wenig Zeichen");
            System.exit(0);
        }
        this.delimiter = this.regex.charAt(0) + "";
        this.regex = this.regex.substring(1, this.regex.length());
        this.idIndex = this.regex.indexOf("I");
        if (this.idIndex < 0) {
            System.out.println("Falsche oder fehlende Eingabe für die Indexspalte");
            System.exit(0);
        }
        this.maxIndex = this.regex.indexOf("M");
        if (this.maxIndex < 0) {
            System.out.println("Falsche oder fehlende Eingabe für die Maximalspalte");
            System.exit(0);
        }
        this.minIndex = this.regex.indexOf("m");
        if (this.minIndex < 0) {
            System.out.println("Falsche oder fehlende Eingabe für die Minimalspalte");
            System.exit(0);
        }
    }

    /**
     * Liest alle Projekte und gibt die Liste aller gelesenen Projekte zurueck
     *
     * @return Liste der Projekte
     */
    public ArrayList<Projekt> getProjekte() {
        if (projektList != null) {
            return projektList;
        } else {
            System.out.println("Lade Projekte");
            loadProjekte();
            return projektList;
        }
    }

    /**
     * Liest alle Projekte und initialisiert die Objekte
     */
    private void loadProjekte() {
        projektList = new ArrayList<>();
        String line;
        Projekt aktProjekt;

        try {
            //Auslesen der Datei
            while ((line = reader.readLine()) != null) {
                String[] elements = line.split(this.delimiter);

                int id = Integer.parseInt(elements[this.idIndex]);
                int max = Integer.parseInt(elements[this.maxIndex]);
                int min = Integer.parseInt(elements[this.minIndex]);
                //Erstellen des Objektes und hinzufuegen zur Liste
                aktProjekt = new Projekt(id, min, max);
                projektList.add(aktProjekt);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

}