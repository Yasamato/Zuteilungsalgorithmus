import "~/app/globals.css"
import { Footer } from "~/components/layout/Footer"
import { Navbar } from "~/components/layout/Navbar"

export const metadata = {
  title: "AG Wahlen",
  description: "Zuteilungsalgorithmus für die Wahlen von AGs",
  icons: [{ rel: "icon", url: "/favicon.ico" }],
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="de">
      <head>
        <meta charSet="utf-8" />
        <meta
          name="viewport"
          content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no"
        />

        <meta
          name="github"
          content="https://github.com/Yasamato/Zuteilungsalgorithmus/"
        />
        <meta name="author" content="Leo Jung" />
        <meta name="author" content="Fabian von der Warth" />
      </head>
      <body className="flex min-h-screen flex-col bg-white dark:bg-gray-900 dark:text-white">
        <Navbar />
        <main className="flex grow flex-col items-center justify-center">
          {children}
        </main>
        <Footer />
      </body>
    </html>
  )
}
