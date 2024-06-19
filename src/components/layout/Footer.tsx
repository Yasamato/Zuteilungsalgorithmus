import Link from "next/link"

export function Footer() {
  return (
    <footer className="flex flex-col justify-between px-4 py-2 sm:flex-row dark:bg-gray-800">
      <span>
        (c) 2024 <Link href={"https://github.com/Yasamato"}>Leo Jung</Link> &{" "}
        <Link href={"https://github.com/fabianvdW"}>Fabian von der Warth</Link>
      </span>
      <Link href={"https://github.com/Yasamato/Zuteilungsalgorithmus"}>
        GitHub
      </Link>
    </footer>
  )
}
