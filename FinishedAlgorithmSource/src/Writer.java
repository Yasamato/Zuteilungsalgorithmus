import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.util.ArrayList;
import java.util.Comparator;
import java.util.Set;
import java.util.TreeSet;


public class Writer {
    /**
     * Schreibt alle Schueler sortiert nach ihren Klassen als Ausgabe in einer CSV-Datei. Die CSV-Datei enthaelt in dieser Reihenfolge die Spalten 'Klasse','Name','Vorname','Zugeteiltes Projekt Nr.', 'Wunschposition des Projektes'
     *
     * @param schueler Alle Schueler, die in die CSV-Datei geschrieben werden
     */
    public static void writeSchuelerListe(ArrayList<Schueler> schueler) {
        try {
            File file = new File("verteilungNachSchuelern.csv");
            if (!file.exists())
                file.createNewFile();
            if (file.exists())
                file.delete();
            FileWriter fw = new FileWriter(file);
            BufferedWriter bw = new BufferedWriter(fw);
            bw.write("Klasse, Name, Vorname, Projekt Nr., Wunsch");
            Set<Schueler> sortedSchueler = new TreeSet<Schueler>(new Comparator<Schueler>() {
                @Override
                public int compare(Schueler o1, Schueler o2) {
                    String s1 = o1.getKlasse() + o1.getName() + o1.getVorname();
                    String s2 = o2.getKlasse() + o2.getName() + o2.getVorname();
                    return s1.compareTo(s2);
                }
            });
            for (Schueler s : schueler) {
                sortedSchueler.add(s);
            }
            for (Schueler s : sortedSchueler) {
                bw.newLine();
                if (s.getZugeteiltesProjekt() != null)
                    bw.write(s.getKlasse() + "," + s.getName() + "," + s.getVorname() + "," + s.getZugeteiltesProjekt().getId() + "," + s.getWahlPosition(s.getZugeteiltesProjekt()));
                else {
                    bw.write(s.getKlasse() + "," + s.getName() + "," + s.getVorname() + ",null,null");
                }
            }
            bw.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Schreibt alle Schueler sortiert nach ihren Projekten in eine CSV-Datei. Die CSV-Datei enthaelt in dieser Reihenfolge die Spalten 'Projekt Nr.', 'Schueler Nachname','Schueler Vorname'
     *
     * @param schueler Alle Schueler, die sortiert als Ausgabe geschrieben werden sollen
     */
    public static void writeProjektListe(ArrayList<Schueler> schueler) {
        try {
            File file = new File("verteilungNachProjekten.csv");
            if (!file.exists())
                file.createNewFile();
            if (file.exists())
                file.delete();
            FileWriter fw = new FileWriter(file);
            BufferedWriter bw = new BufferedWriter(fw);
            bw.write("Projekt Nr.,Schueler Nachname,Schueler Vorname");
            Set<Schueler> sortedProjekte = new TreeSet<Schueler>(new Comparator<Schueler>() {
                @Override
                public int compare(Schueler o1, Schueler o2) {
                    String s1 = "";
                    String s2 = "";
                    if (!(o1.getZugeteiltesProjekt() == null) && !(o2.getZugeteiltesProjekt() == null)) {
                        s1 = Integer.toString(o1.getZugeteiltesProjekt().getId());
                        s2 = Integer.toString(o2.getZugeteiltesProjekt().getId());
                        if (s1.length() == 1) {
                            s1 = "0" + s1;
                        }
                        if (s2.length() == 1) {
                            s2 = "0" + s2;
                        }
                        s1 = s1 + o1.getName() + o1.getVorname();
                        s2 = s2 + o2.getName() + o2.getVorname();
                    } else {
                        if (o1.getZugeteiltesProjekt() == null)
                            return -1;
                        else if (o2.getZugeteiltesProjekt() == null)
                            return 1;
                    }
                    return s1.compareTo(s2);
                }
            });
            for (Schueler s : schueler) {
                sortedProjekte.add(s);
            }

            for (Schueler s : sortedProjekte) {
                bw.newLine();
                if (s.getZugeteiltesProjekt() != null)
                    bw.write(s.getZugeteiltesProjekt().getId() + "," + s.getName() + "," + s.getVorname());
                else {
                    bw.write("null," + s.getName() + "," + s.getVorname());
                }
            }
            bw.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
